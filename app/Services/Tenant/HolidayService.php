<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\DTOs\HolidayDTO;
use App\DTOs\HolidayEditDTO;
use App\DTOs\HolidayRequestDTO;
use App\DTOs\HolidayStoreDTO;
use App\Mail\HolidayApprove;
use App\Models\GeneralSetting;
use App\Models\Holiday;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class HolidayService
{

    protected SendMailAction $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }

    /**
     * Retrieve holidays based on user permissions.
     *
     * @param int $userId
     * @param bool $canApprove
     * @return array
     */
    public function getHolidaysForUser(int $userId, bool $canApprove): array
    {
        try {
            $query = Holiday::with('user') // تحميل العلاقة مرة واحدة
            ->select(['id', "user_id", "from_date", "to_date", "note", "is_approved", 'created_at'])
                ->orderBy('id', 'desc');

            if (!$canApprove) {
                $query->where('user_id', $userId);
            }

            $holidays = $query->latest('id')->get();

            return HolidayDTO::collection($holidays);
        } catch (\Exception $e) {
            Log::error('Error fetching holidays: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Store a new holiday record.
     *
     * @param HolidayStoreDTO $dto
     * @return Holiday|null
     * @throws \Exception
     */
    public function createHoliday(HolidayStoreDTO $dto)
    {
        try {
            // Attempt to create a new holiday using data from the DTO
            return Holiday::create($dto->toArray());
        } catch (\Exception $e) {
            // Log any error that occurs during the creation process
            Log::error('Failed to create holiday: ' . $e->getMessage());
            return null; // Return null if creation fails
        }
    }

    /**
     * Approve a holiday request.
     *
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function approveHoliday(int $id): string
    {
        DB::beginTransaction();
        try {
            // Attempt to find the holiday by its ID
            $holiday = Holiday::findOrFail($id);

            // Update the holiday's approval status
            $holiday->update(['is_approved' => true]);

            // Prepare email data for notification
            $mail_data = [
                'name'  => $holiday->user->name,
                'email' => $holiday->user->email,
            ];

            // Send approval notification email
            $message = $this->sendMailAction->sendMail($mail_data, HolidayApprove::class);

            DB::commit(); // Commit the transaction if everything succeeds
            return $message; // Return the success message
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Holiday not found.'); // Rethrow if the holiday is not found
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve holiday: ' . $e->getMessage()); // Log any other errors
            throw new \Exception('Failed to approve holiday.'); // Rethrow the exception
        }
    }

    /**
     * Get a user's holidays for a specific month and year.
     *
     * @param HolidayRequestDTO $dto
     * @return array
     * @throws \Exception
     */
    public function getUserHolidays(HolidayRequestDTO $dto): array
    {
        DB::beginTransaction();
        try {
            // Determine the start and end dates for the month
            $start_date = Carbon::create($dto->year, $dto->month, 1);
            $end_date = $start_date->copy()->endOfMonth();
            $daysInMonth = $start_date->daysInMonth;

            // Fetch all holidays for the user within the specified month range
            $holidays = Holiday::whereBetween('from_date', [$start_date, $end_date])
                ->orWhereBetween('to_date', [$start_date, $end_date])
                ->where('is_approved', true)
                ->where('user_id', $dto->user_id)
                ->get(['from_date', 'to_date']);

            // Retrieve the date format setting with caching for performance optimization
            $date_format = Cache::remember('date_format', 3600, function () {
                return GeneralSetting::pluck('date_format')->first() ?? 'Y-m-d';
            });

            // Process each day of the month to determine if it's a holiday
            $holidayDays = collect(range(1, $daysInMonth))->mapWithKeys(function ($day) use ($holidays, $dto, $date_format) {
                $date = Carbon::create($dto->year, $dto->month, $day)->format('Y-m-d');
                $holiday = $holidays->first(fn($h) => $date >= $h->from_date && $date <= $h->to_date);

                // Return formatted holiday info or false if not a holiday
                return [$day => $holiday ? Carbon::parse($holiday->from_date)->format($date_format) . ' ' . trans("file.To") . ' ' . Carbon::parse($holiday->to_date)->format($date_format) : false];
            });

            DB::commit(); // Commit transaction if everything goes well

            // Return the holiday data and other calendar info
            return [
                'start_day'    => $start_date->dayOfWeek + 1,
                'year'         => $dto->year,
                'month'        => $dto->month,
                'number_of_day'=> $daysInMonth,
                'prev_year'    => $start_date->copy()->subMonth()->year,
                'prev_month'   => $start_date->copy()->subMonth()->month,
                'next_year'    => $start_date->copy()->addMonth()->year,
                'next_month'   => $start_date->copy()->addMonth()->month,
                'holidays'     => $holidayDays->toArray(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error fetching holidays: ' . $e->getMessage()); // Log error
            throw new \Exception('Failed to fetch holidays.'); // Rethrow exception
        }
    }

    /**
     * Update an existing holiday record.
     *
     * @param HolidayEditDTO $dto
     * @return void
     * @throws \Exception
     */
    public function updateHoliday(HolidayEditDTO $dto)
    {
        try {
            // Attempt to find the holiday by its ID and update it
            $holiday_data = Holiday::findOrFail($dto->id);
            $holiday_data->update($dto->toArray());
        } catch (ModelNotFoundException $e) {
            // Log and rethrow the exception if the holiday is not found
            Log::error('Holiday not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log and rethrow for general errors
            Log::error('Failed to updating holiday: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete multiple holidays by their IDs.
     *
     * @param array $holidaytId
     * @return bool
     * @throws \Exception
     */
    public function deleteHolidays(array $holidaytId): bool
    {
        try {
            // Attempt to delete multiple holidays
            Holiday::whereIn('id', $holidaytId)->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            // Log error and rethrow if holidays are not found
            Log::error('Holiday not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log and rethrow for general errors
            Log::error('Error deleting Holiday: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a holiday by its ID.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteHoliday(int $id): bool
    {
        try {
            // Attempt to find and delete a holiday by its ID
            Holiday::findOrFail($id)->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            // Log error and rethrow if holiday is not found
            Log::error('Holiday not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log and rethrow for general errors
            Log::error('Error deleting Holiday: ' . $e->getMessage());
            throw $e;
        }
    }
}

