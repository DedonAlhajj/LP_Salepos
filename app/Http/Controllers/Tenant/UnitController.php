<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UnitRequest;
use App\Imports\UnitImport;
use App\Services\Tenant\ImportService;
use App\Services\Tenant\UnitService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected UnitService $unitService;
    protected ImportService $importService;

    public function __construct(UnitService $unitService,ImportService $importService)
    {
        $this->unitService = $unitService;
        $this->importService = $importService;
    }

    /**
     * Display the Unit index page with Unit data.
     *
     * This method retrieves the Unit data for the authenticated user
     * and returns the view for the Unit index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorize the user to access the 'Unit-index' permission.
            $this->authorize('unit');
            // Get Unit data for the logged-in user from the service
            $unit_all = $this->unitService->getUnitWithoutTrashed();

            // Return the view with the Unit data
            return view('Tenant.unit.create', compact('unit_all'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading Unit data.')]);
        }
    }

    /**
     * Store new Unit data in the system.
     *
     * This method validates the incoming request data and stores the Unit record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param UnitRequest $request
     * @return RedirectResponse
     */
    public function store(UnitRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->unitService->createUnit($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Unit created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to create Unit, please try again.');
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            // Return a Unit data in the response.
            return response()->json($this->unitService->edit($id));
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to get Unit data!');
        } catch (\Exception $e) {
            return response()->json('Failed to get Unit data!');
        }
    }
    /**
     * Update new Unit data in the system.
     *
     * This method validates the incoming request data and updates the Unit record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param UnitRequest $request
     * @return RedirectResponse
     */

    public function update(UnitRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->unitService->updateUnit($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Unit updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to update Unit, please try again.');
        }
    }

    /**
     * Import Unit data from an uploaded file.
     *
     * @param Request $request The incoming HTTP request containing the file to import.
     * @return RedirectResponse Redirects back with a success or error message.
     *
     * This function utilizes the import service to process Unit data
     * from the uploaded file. In case of an error, it catches the exception and
     * returns an error message.
     */
    public function importUnit(Request $request): RedirectResponse
    {
        try {
            $this->importService->import(UnitImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Delete multiple Unit by selection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected Unit IDs to the service for deletion.
            $this->unitService->deleteUnit($request->input('unitIdArray'));

            // Return a success message in the response.
            return response()->json('Unit deleted successfully!');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($exception->getMessage());
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a single Unit record by date and Unit ID.
     *
     * This method deletes the Unit record for a specific Unit on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the Unit with the specified date and Unit ID
            $this->unitService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Unit deleted successfully');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }


}
