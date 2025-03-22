<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CustomerGroupRequest;
use App\Imports\CustomerGroupImport;
use App\Services\Tenant\CustomerGroupService;
use App\Services\Tenant\ImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    protected CustomerGroupService $customerGroupService;
    protected ImportService $importService;

    public function __construct(CustomerGroupService $customerGroupService,ImportService $importService)
    {
        $this->customerGroupService = $customerGroupService;
        $this->importService = $importService;
    }


    /**
     * Display the CustomerGroup index page with CustomerGroup data.
     *
     * This method retrieves the CustomerGroup data for the authenticated user
     * and returns the view for the CustomerGroup index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorize the user to ensure they have permission to view Customer Groups
            $this->authorize('customer_group');
            // Get CustomerGroup data for the logged-in user from the service
            $customer_group_all = $this->customerGroupService->getActiveCustomerGroup();

            // Return the view with the CustomerGroup data
            return view('Tenant.customer_group.create', compact('customer_group_all'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading CustomerGroup data.')]);
        }
    }

    /**
     * Store new CustomerGroup data in the system.
     *
     * This method validates the incoming request data and stores the CustomerGroup record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param CustomerGroupRequest $request
     * @return RedirectResponse
     */
    public function store(CustomerGroupRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->customerGroupService->storeCustomerGroup($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'CustomerGroup created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to create CustomerGroup, please try again.');
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            // Return a customer group data in the response.
            return response()->json($this->customerGroupService->edit($id));
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to delete employees!');
        }
    }

    /**
     * Update new CustomerGroup data in the system.
     *
     * This method validates the incoming request data and updates the CustomerGroup record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param CustomerGroupRequest $request
     * @return RedirectResponse
     */
    public function update(CustomerGroupRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->customerGroupService->updateCustomerGroup($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'CustomerGroup updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to update CustomerGroup, please try again.');
        }
    }

    /**
     * Import customer group data from an uploaded file.
     *
     * @param Request $request The incoming HTTP request containing the file to import.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success or error message.
     *
     * This function utilizes the import service to process customer group data
     * from the uploaded file. In case of an error, it catches the exception and
     * returns an error message.
     */
    public function importCustomerGroup(Request $request): RedirectResponse
    {
        try {
            $this->importService->import(CustomerGroupImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Export customer group data to a file.
     *
     * @param Request $request The incoming HTTP request containing data to export.
     * @return \Illuminate\Http\JsonResponse Returns a JSON response with success or error message.
     *
     * This function calls the export service to generate a customer group export file,
     * using the provided data array. In case of an error, it catches the exception and
     * returns the error message as a JSON response.
     */
    public function exportCustomerGroup(Request $request): JsonResponse
    {
        try {
            $this->importService->export($request['customer_groupArray'], "CustomerGroup", ['Name', 'Percentage']);
            return response()->json("'Data Exported successfully");
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
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
            $this->customerGroupService->deleteCustomerGroup($request->input('customer_groupIdArray'));

            // Return a success message in the response.
            return response()->json('CustomerGroup deleted successfully!');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($exception->getMessage());
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a single CustomerGroup record by date and CustomerGroup ID.
     *
     * This method deletes the CustomerGroup record for a specific CustomerGroup on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the CustomerGroup with the specified date and CustomerGroup ID
            $this->customerGroupService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'CustomerGroup deleted successfully');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }


}
