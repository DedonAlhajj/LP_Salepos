<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PayrollRequest;
use App\Services\Tenant\PayrollService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{

    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Display the payroll index page with attendance data.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorization check: Ensure the user has the 'payroll' permission
            $this->authorize('payroll');

            // Retrieve payroll-related attendance data for the logged-in user from the service
            $attendanceData = $this->payrollService->getAttendanceData(Auth::user());

            // Return the payroll index view with the retrieved data
            return view('Tenant.payroll.index', $attendanceData);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with an error message
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading payroll data.')]);
        }
    }

    /**
     * Store a newly created payroll record.
     *
     * @param PayrollRequest $request The validated request data.
     * @return RedirectResponse
     */
    public function store(PayrollRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the payroll service for processing
            $this->payrollService->storePayroll($request->validated());

            // Redirect back with a success message upon successful creation
            return redirect()->back()->with('message', 'Payroll created successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with an error message
            return redirect()->back()->with('error', 'Failed to create payroll, please try again.');
        }
    }

    /**
     * Update an existing payroll record.
     *
     * @param PayrollRequest $request The validated request data.
     * @param int $id The ID of the payroll record to update.
     * @return RedirectResponse
     */
    public function update(PayrollRequest $request, int $id): RedirectResponse
    {
        try {
            // Pass the validated request data to the payroll service for updating
            $this->payrollService->updatePayroll($request->validated());

            // Redirect back with a success message upon successful update
            return redirect()->back()->with('message', 'Payroll updated successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with an error message
            return redirect()->back()->with('error', 'Failed to update payroll, please try again.');
        }
    }

    /**
     * Delete multiple payroll records based on the selected IDs.
     *
     * @param Request $request The request containing an array of payroll IDs.
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected payroll IDs to the payroll service for deletion
            $this->payrollService->deletePayrolls($request->input('payrollIdArray'));

            // Return a success message in the JSON response
            return response()->json('Payroll deleted successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where a payroll record is not found
            return response()->json('Payroll not found!');
        } catch (\Exception $e) {
            // Handle any general exceptions and return a failure message
            return response()->json('Failed to delete payroll!');
        }
    }

    /**
     * Delete a specific payroll record by ID.
     *
     * @param int $id The ID of the payroll record to delete.
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the payroll service to delete the specified payroll record
            $this->payrollService->deletePayroll($id);

            // Redirect back with a success message upon successful deletion
            return redirect()->back()->with('message', 'Payroll deleted successfully');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the payroll record is not found
            return redirect()->back()->with(['not_permitted' => 'Payroll not found!']);
        } catch (\Exception $e) {
            // Handle any general exceptions and return an appropriate error message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete Payroll. ' . $e->getMessage()]);
        }
    }

}
