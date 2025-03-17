<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\EmployeeDTO;
use App\DTOs\EmployeeEditDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\EmployeeEditRequest;
use App\Http\Requests\Tenant\EmployeeRequest;
use App\Services\Tenant\EmployeeServices;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{

    protected EmployeeServices $employeeServices;

    public function __construct(EmployeeServices $employeeServices)
    {
        $this->employeeServices = $employeeServices;
    }

    /**
     * Display a listing of employees.
     *
     * @return View
     * @throws AuthorizationException
     */
    public function index(): View
    {
        // Authorize the user to access the 'employees-index' permission.
        $this->authorize('employees-index');

        // Fetch employee data through the EmployeeService.
        $data = $this->employeeServices->index();

        // Return the employee listing view with the fetched data.
        return view('Tenant.employee.index', $data);
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return View|Application|Factory|RedirectResponse
     */
    public function create(): View|Application|Factory|RedirectResponse
    {
        try {
            // Authorize the user to access the 'employees-add' permission.
            $this->authorize('employees-add');

            // Fetch data required for the employee creation form through the EmployeeService.
            $data = $this->employeeServices->create();

            // Return the employee creation view with the required data.
            return view('Tenant.employee.create', $data);
        } catch (\Exception $e) {
            // Handle any exceptions that occur and provide a meaningful error message.
            return redirect()->back()->with('not_permitted', 'An error occurred while fetching data');
        }
    }

    /**
     * Store a newly created employee in the database.
     *
     * @param EmployeeRequest $request
     * @return RedirectResponse
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        try {
            // Convert validated request data into a Data Transfer Object (DTO).
            $dto = EmployeeDTO::fromRequest($request->validated());

            // Pass the DTO to the service to create the new employee.
            $this->employeeServices->createEmployee($dto);

            // Redirect back to the employees index page with a success message.
            return redirect('employees')->with('message', 'Employee created successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for storing data.
            return redirect()->back()->with('errors', "An error occurred while storing data: " . $e->getMessage());
        }
    }

    /**
     * Update an existing employee in the database.
     *
     * @param EmployeeEditRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(EmployeeEditRequest $request, $id): RedirectResponse
    {
        try {
            // Convert the validated request data into an edit DTO.
            $dto = EmployeeEditDTO::fromRequest($request->validated());

            // Use the service to update the employee with the given ID.
            $this->employeeServices->updateEmployee($dto, $id);

            // Redirect back to the employees index page with a success message.
            return redirect('employees')->with('message', 'Employee updated successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for updating data.
            return redirect()->back()->with('not_permitted', "An error occurred while updating the employee.");
        }
    }

    /**
     * Delete multiple employees by selection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected employee IDs to the service for deletion.
            $this->employeeServices->deleteEmployees($request->input('employeeIdArray'));

            // Return a success message in the response.
            return response()->json('Employee deleted successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to delete employees!');
        }
    }

    /**
     * Remove the specified employee from the database.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the employee with the specified ID.
            $this->employeeServices->deleteEmployee($id);

            // Redirect back with a success message.
            return redirect()->back()->with('message', 'Department deleted successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => 'Failed to delete department. ' . $e->getMessage()]);
        }
    }

}
