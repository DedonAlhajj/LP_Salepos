<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\WarehousesRequest;
use App\Imports\WarehouseImport;
use App\Services\Tenant\ImportService;
use App\Services\Tenant\WarehouseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;
use App\Traits\CacheForget;

class WarehouseController extends Controller
{
    private WarehouseService $warehouseService;
    private ImportService $importService;

    public function __construct(WarehouseService $warehouseService,ImportService $importService)
    {
        $this->warehouseService = $warehouseService;
        $this->importService = $importService;

    }

    /**
     * Display the list of warehouses.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Retrieve warehouse data using the service layer
            $data = $this->warehouseService->getDataIndex();
            return view('Tenant.warehouse.create', $data);
        } catch (\Exception $ex) {
            // Return an error message if fetching warehouses fails
            return back()->with('not_permitted', 'Failed to load warehouse. Please try again.');
        }
    }

    /**
     * Store a newly created warehouse in the database.
     *
     * @param WarehousesRequest $request
     * @return RedirectResponse
     */
    public function store(WarehousesRequest $request): RedirectResponse
    {
        try {
            // Create a warehouse using the service layer
            $this->warehouseService->createWarehouse($request->validated());

            return redirect('warehouse')->with('message', 'Data inserted successfully');
        } catch (\Exception $e) {
            // Handle any exception that occurs while creating the warehouse
            return back()->with(['not_permitted' => 'An error occurred while creating the warehouse.']);
        }
    }

    /**
     * Retrieve and return warehouse details for editing.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        try {
            // Retrieve warehouse details using the service layer
            $warehouse = $this->warehouseService->edit($id);
            return response()->json($warehouse);
        } catch (\Exception $e) {
            // Handle any exception and return an error response
            return response()->json('Error while fetching the warehouse details. Please try again.');
        }
    }

    /**
     * Update the specified warehouse in the database.
     *
     * @param WarehousesRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(WarehousesRequest $request, int $id): RedirectResponse
    {
        try {
            // Update warehouse details using the service layer
            $this->warehouseService->updateWarehouse($request->validated());

            return redirect('warehouse')->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            // Handle any exception that occurs while updating the warehouse
            return back()->with(['not_permitted' => 'An error occurred while updating the warehouse.']);
        }
    }

    /**
     * Import warehouses from an uploaded file.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function importWarehouse(Request $request): RedirectResponse
    {
        try {
            // Import warehouse data using the import service
            $this->importService->import(WarehouseImport::class, $request->file('file'));

            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            // Handle any exception that occurs during import
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete multiple warehouses by selection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected warehouse IDs to the service for deletion.
            $this->warehouseService->deleteWarehouses($request->input('warehouseIdArray'));

            // Return a success message in the response.
            return response()->json('warehouse deleted successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to delete warehouses!');
        }
    }

    /**
     * Remove the specified warehouse from the database.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the warehouse with the specified ID.
            $this->warehouseService->deleteWarehouse($id);

            // Redirect back with a success message.
            return redirect()->back()->with('message', 'Warehouse deleted successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => 'Failed to delete warehouse. ' . $e->getMessage()]);
        }
    }

}
