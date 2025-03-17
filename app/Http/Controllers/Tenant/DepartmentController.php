<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DepartmentRequest;
use App\Services\Tenant\DepartmentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{

    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display a listing of active departments.
     *
     * @return View
     * @throws AuthorizationException
     */
    public function index()
    {
        // Check if the user has permission to access the department module
        $this->authorize('department');

        // Retrieve all active departments using the service layer
        $departments = $this->departmentService->getActiveDepartment();

        // Return the department index view with the retrieved departments
        return view('Tenant.department.index', compact('departments'));
    }

    /**
     * Store a newly created department.
     *
     * @param DepartmentRequest $request
     * @return RedirectResponse
     */
    public function store(DepartmentRequest $request): RedirectResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validated();

        try {
            // Store the department using the service layer
            $this->departmentService->storeDepartment($validatedData);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Department created successfully');
        } catch (\Exception $e) {
            // Handle errors and provide meaningful feedback
            return redirect()->back()->with('not_permitted', 'An error occurred while processing the department: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified department.
     *
     * @param DepartmentRequest $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(DepartmentRequest $request, int $id): RedirectResponse
    {
        try {
            // Validate the request data
            $data = $request->validated();

            // Update the department using the service layer
            $this->departmentService->updateDepartment($data);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Department updated successfully');
        } catch (\Exception $e) {
            // Catch and handle errors, returning a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to update department. ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple departments by selection.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteBySelection(Request $request): RedirectResponse
    {
        try {
            // Retrieve the department IDs from the request and pass them to the service layer
            $this->departmentService->deleteDepartment($request->input('departmentIdArray'));

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Departments deleted successfully');
        } catch (\Exception $e) {
            // Catch and handle errors, returning a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete departments. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified department.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Delete the department using the service layer
            $this->departmentService->deleteDepartment($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Department deleted successfully');
        } catch (\Exception $e) {
            // Catch and handle errors, returning a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete department. ' . $e->getMessage()]);
        }
    }

}
