<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\HolidayEditDTO;
use App\DTOs\HolidayRequestDTO;
use App\DTOs\HolidayStoreDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HolidayRequest;
use App\Services\Tenant\HolidayService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HolidayController extends Controller
{

    private HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Display a listing of holidays.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Check authorization efficiently
            $approvePermission = Gate::allows('holiday');

            // Get holidays with optimized queries
            $holidays = $this->holidayService->getHolidaysForUser(auth()->id(), $approvePermission);

            return view('Tenant.holiday.index', [
                'lims_holiday_list' => $holidays,
                'approve_permission' => $approvePermission
            ]);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'Failed to load holidays. Please try again.');
        }
    }

    /**
     * Store a new holiday.
     *
     * This method processes the holiday creation request, validates the input,
     * and delegates the creation process to the holiday service. Upon success,
     * it redirects to the holiday index page with a success message.
     *
     * @param HolidayRequest $request The validated holiday request data.
     * @return RedirectResponse Redirects to the holiday index page with a success message.
     */
    public function store(HolidayRequest $request): RedirectResponse
    {
        try {
            // Create a Data Transfer Object (DTO) from the validated request data.
            $dto = HolidayStoreDTO::fromRequest($request->validated());

            // Delegate the holiday creation to the holiday service.
            $holiday = $this->holidayService->createHoliday($dto);

            // Redirect to the holidays index page with a success message.
            return redirect()->route('holidays.index')->with('message', 'Holiday created successfully.');
        } catch (\Exception $e) {
            // In case of any error, redirect back with an error message.
            return redirect()->route('holidays.index')->with('not_permitted', 'Unexpected error occurred.');
        }
    }

    /**
     * Approve a holiday request.
     *
     * This method calls the holiday service to approve the holiday. It returns a
     * JSON response indicating whether the holiday was approved successfully or not.
     *
     * @param int $id The ID of the holiday to approve.
     * @return JsonResponse JSON response with the result of the approval process.
     */
    public function approveHoliday($id): JsonResponse
    {
        try {
            // Delegate the holiday approval process to the holiday service.
            $result = $this->holidayService->approveHoliday($id);

            // Return the result as a JSON response.
            return response()->json($result);
        } catch (\Exception $e) {
            // Return a failure message if an error occurs during approval.
            return response()->json('Failed to approve holiday.');
        }
    }

    /**
     * Display holidays for a specific user in a given year and month.
     *
     * This method retrieves the holidays for the authenticated user based on the
     * provided year and month and returns a view displaying the user's holidays.
     *
     * @param int $year The year to fetch holidays for.
     * @param int $month The month to fetch holidays for.
     * @return View|RedirectResponse
     */
    public function myHoliday($year, $month): View|RedirectResponse
    {
        try {
            // Create a Data Transfer Object (DTO) for the request.
            $dto = new HolidayRequestDTO($year, $month, Auth::id());

            // Fetch the holiday data using the holiday service.
            $holidaysData = $this->holidayService->getUserHolidays($dto);

            // Return the view displaying the holidays for the user.
            return view('Tenant.holiday.my_holiday', $holidaysData);
        } catch (\Exception $e) {
            // Return an error message if holidays data retrieval fails.
            return redirect()->back()->withErrors('Failed to retrieve holiday data.');
        }
    }

    /**
     * Update an existing holiday.
     *
     * This method processes the update request for an existing holiday. It validates
     * the input and delegates the update process to the holiday service. Upon success,
     * it redirects to the holiday index page with a success message.
     *
     * @param HolidayRequest $request The validated holiday request data.
     * @param int $id The ID of the holiday to update.
     * @return RedirectResponse Redirects to the holiday index page with a success message.
     */
    public function update(HolidayRequest $request, $id): RedirectResponse
    {
        try {
            // Create a Data Transfer Object (DTO) from the validated request data.
            $dto = HolidayEditDTO::fromRequest($request->validated());

            // Delegate the holiday update process to the holiday service.
            $this->holidayService->updateHoliday($dto);

            // Redirect to the holidays index page with a success message.
            return redirect()->route('holidays.index')->with('message', 'Holiday updated successfully.');
        } catch (\Exception $e) {
            // In case of any error, redirect back with an error message.
            return redirect()->route('holidays.index')->with('not_permitted', 'Unexpected error occurred.');
        }
    }

    /**
     * Delete selected holidays.
     *
     * This method processes the deletion of multiple holidays by their IDs.
     * It delegates the deletion process to the holiday service and returns
     * a JSON response indicating success or failure.
     *
     * @param Request $request The request containing the IDs of the holidays to delete.
     * @return JsonResponse JSON response indicating the result of the deletion process.
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected Holiday IDs to the Holiday service for deletion.
            $this->holidayService->deleteHolidays($request->input('holidayIdArray'));

            // Return a success message in the JSON response.
            return response()->json('Holiday deleted successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where a Holiday record is not found.
            return response()->json('Holiday not found!');
        } catch (\Exception $e) {
            // Handle any general exceptions and return a failure message.
            return response()->json('Failed to delete Holiday!');
        }
    }

    /**
     * Delete a single holiday.
     *
     * This method deletes a specific holiday record by its ID. It returns a
     * redirect response with a success or error message based on the outcome.
     *
     * @param int $id The ID of the holiday to delete.
     * @return RedirectResponse Redirects back with a success or error message.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the Holiday service to delete the specified Holiday record.
            $this->holidayService->deleteHoliday($id);

            // Redirect back with a success message upon successful deletion.
            return redirect()->back()->with('message', 'Holiday deleted successfully');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the Holiday record is not found.
            return redirect()->back()->with(['not_permitted' => 'Holiday not found!']);
        } catch (\Exception $e) {
            // Handle any general exceptions and return an appropriate error message.
            return redirect()->back()->with(['not_permitted' => 'Failed to delete Holiday. ' . $e->getMessage()]);
        }
    }

}
