<?php

namespace App\Services\Tenant;

use App\DTOs\AttendanceDTO;
use App\DTOs\AttendanceStoreDTO;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\HrmSetting;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class AttendanceService
{

    /**
     * Fetches the attendance data for the specified user, caching the result for performance.
     *
     * This function retrieves the attendance data for the user, including employee list, HRM settings, and
     * attendance records. The attendance records are grouped by date and employee ID. It uses caching to
     * avoid redundant database queries.
     *
     * @return array  Returns an array containing employee list, HRM settings, and attendance data.
     */
    public function getAttendanceData($user): array
    {
        $cacheKey = 'attendance_data_' . $user->id;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            // Fetch employees, HRM settings, and general settings
            $employees = Employee::select(['id', 'name'])->get();
            $hrmSetting = HrmSetting::latest()->first();
            $generalSetting = DB::table('general_settings')->latest()->first();

            // Fetch attendance records with relationships and apply access control based on role
            $attendanceRecords = Attendance::with(['employee:id,name', 'user:id,name'])
                ->when(!$user->hasRole(['Admin', 'Owner']) && $generalSetting->staff_access == 'own', function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
                ->orderByDesc('date')
                ->get()
                ->groupBy(['date', 'employee_id']);

            // Transform attendance records into a clean array format
            $attendanceData = array_map(fn (AttendanceDTO $dto) => $dto->toArray(), $this->transformAttendanceData($attendanceRecords));

            return [
                'lims_employee_list' => $employees,
                'lims_hrm_setting_data' => $hrmSetting,
                'lims_attendance_all' => $attendanceData, // Attendance data now in array format
            ];
        });
    }

    /**
     * Transforms attendance records into a structured array of DTOs.
     *
     * @param Collection $attendanceRecords  The grouped attendance records to transform.
     * @return array  The transformed array of attendance data.
     */
    #[Pure] private function transformAttendanceData($attendanceRecords): array
    {
        $result = [];

        // Iterate through the attendance records and transform each record into a DTO
        foreach ($attendanceRecords as $date => $employeeRecords) {
            foreach ($employeeRecords as $employeeId => $records) {
                $checkinCheckout = [];
                $status = null;
                $employeeName = null;
                $userName = null;

                // Process check-in/check-out times and other information
                foreach ($records as $record) {
                    $checkinCheckout[] = ($record->checkin ?? 'N/A') . ' - ' . ($record->checkout ?? 'N/A');
                    $status = $record->status;
                    $employeeName = $record->employee->name ?? 'Unknown';
                    $userName = $record->user->name ?? 'Unknown';
                }

                // Create a DTO object for the attendance data
                $result[] = (new AttendanceDTO(
                    $date,
                    $employeeName,
                    implode('<br>', $checkinCheckout),
                    $status,
                    $userName,
                    $employeeId
                ));
            }
        }

        return $result;
    }

    /**
     * Stores attendance records in the database.
     *
     * This function processes and stores multiple attendance records for employees. It validates
     * the date format and ensures that the attendance is stored efficiently using batch inserts.
     *
     * @param array $data The data containing employee IDs, attendance check-in/check-out times, and status.
     * @return bool  Returns true if the operation was successful, false otherwise.
     * @throws Exception
     */
    public function storeAttendance(array $data): bool
    {
        try {
            DB::transaction(function () use ($data) {
                $employeeIds = $data['employee_id'];
                $checkinTime = HrmSetting::latest('id')->value('checkin'); // Single query for faster retrieval
                $cleanDate = trim($data['date']);

                // Validate date format to avoid errors
                try {
                    $date = Carbon::parse($cleanDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    throw new Exception("Invalid date format: $cleanDate");
                }
                $userId = Auth::id();
                $attendances = [];

                // Iterate through each employee ID to create attendance records
                foreach ($employeeIds as $id) {
                    $existingAttendance = Attendance::whereDate('date', $date)
                        ->where('employee_id', $id)
                        ->first();

                    $status = $existingAttendance
                        ? $existingAttendance->status
                        : (strtotime($checkinTime) >= strtotime($data['checkin']) ? 1 : 0);

                    $attendanceDTO = new AttendanceStoreDTO(
                        $date,
                        $id,
                        $userId,
                        $data['checkin'],
                        $data['checkout'] ?? null,
                        $status,
                        $data['note']
                    );

                    $attendances[] = $attendanceDTO->toArray();
                }

                // Perform batch insert for better performance
                Attendance::insert($attendances); // Faster with batch insert
            });
            Cache::forget('attendance_data_' . Auth::user()->id);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to create attendances: ' . $e->getMessage());
            throw new Exception('Failed to create attendances: ' . $e->getMessage());
        }
    }

    /**
     * Deletes multiple attendance records based on the provided data.
     *
     * This function deletes attendance records for specified employee IDs and dates. It also clears
     * the cache after deletion for data consistency.
     *
     * @param array $data The data containing employee IDs and attendance dates to delete.
     * @return bool  Returns true if the deletion was successful, false otherwise.
     * @throws Exception
     */
    public function deleteAttendances(array $data): bool
    {
        DB::beginTransaction();
        try {
            $cacheKey = 'attendance_data_' . Auth::user()->id;
            $dates = $data->pluck(0);
            $employeeIds = $data->pluck(1);

            // Delete attendance records in bulk
            Attendance::whereIn('date', $dates)
                ->whereIn('employee_id', $employeeIds)
                ->delete();

            DB::commit();
            // Clear cache after deletion
            Cache::forget($cacheKey);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Attendance: ' . $e->getMessage());
            throw $e; // Re-throw the exception for handling elsewhere
        }
    }

    /**
     * Deletes a single attendance record for the specified date and employee ID.
     *
     * @param string $date The date of the attendance to delete.
     * @param int $employee_id The ID of the employee whose attendance is to be deleted.
     * @return bool  Returns true if the deletion was successful, false otherwise.
     * @throws Exception
     */
    public function deleteAttendance(string $date, int $employee_id): bool
    {
        try {
            $cacheKey = 'attendance_data_' . Auth::user()->id;
            Attendance::wheredate('date', $date)->where('employee_id', $employee_id)->delete();

            // Clear cache after deletion
            Cache::forget($cacheKey);

            return true;
        } catch (Exception $e) {
            Log::error('Error deleting Attendance: ' . $e->getMessage());
            throw $e; // Re-throw the exception for handling elsewhere
        }
    }





}

