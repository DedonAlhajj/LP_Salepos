<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AdjustmentRequest;
use App\Services\Tenant\AdjustmentService;
use App\Services\Tenant\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdjustmentController extends Controller
{

    private $adjustmentService;
    protected $productService;

    public function __construct(AdjustmentService $adjustmentService,ProductService $productService)
    {
        $this->adjustmentService = $adjustmentService;
        $this->productService = $productService;

    }

    /**
     * Display a list of all adjustments.
     * Retrieves adjustment data from the service layer and returns the appropriate view.
     * If an error occurs, it logs the error and redirects back with an error message.
     *
     * @param Request $request The incoming HTTP request.
     * @return View|RedirectResponse The response view or redirection.
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            // Fetch all adjustment records from the service layer
            $adjustments = $this->adjustmentService->getAllAdjustments();

            // Return the index view with the fetched adjustment data
            return view('Tenant.adjustment.index', compact('adjustments'));
        } catch (\Exception $e) {
            // Log the error and return back with an error message
            Log::error("Error fetching adjustments: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error fetching modifications");
        }
    }

    /**
     * Retrieve products associated with a specific warehouse.
     * Fetches product data based on the warehouse ID and returns it as a JSON response.
     * In case of an error, returns a JSON error message with details.
     *
     * @param int $id The unique identifier of the warehouse.
     * @return JsonResponse The JSON response containing the product data or an error message.
     */
    public function getProduct(int $id): JsonResponse
    {
        try {
            // Retrieve product data from the service layer
            $productData = $this->productService->getProductsByWarehouse($id);

            // Return the retrieved product data in JSON format
            return response()->json($productData, 200);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response with details
            return response()->json(['error' => 'An error occurred while fetching data.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Search for a product by its code.
     * Validates incoming request data, queries the product service, and returns JSON response.
     * If the product is not found, it returns a 404 error message.
     *
     * @param Request $request The incoming request containing the product code.
     * @return JsonResponse The JSON response with the product data or an error message.
     */
    public function limsProductSearch(Request $request): JsonResponse
    {
        // Validate the incoming request to ensure required data is present
        $validated = $request->validate([
            'data' => 'required|string|max:255'
        ]);

        // Perform a search in the product service layer
        $result = $this->productService->searchProduct($validated['data']);

        if (isset($result['error'])) {
            // Return error response if the product is not found
            return response()->json(['message' => $result['error']], 404);
        }

        // Return successful response with the product data
        return response()->json($result);
    }

    /**
     * Show the adjustment creation view.
     * Fetches necessary data for warehouse selection and returns the create view.
     * Logs and returns an error message if an exception occurs.
     *
     * @return View|RedirectResponse The response view or redirection.
     */
    public function create(): View|RedirectResponse
    {
        try {
            // Retrieve data necessary for the creation view
            $warehouses = $this->adjustmentService->getCreateData();

            // Return the create view with the retrieved warehouse data
            return view('Tenant.adjustment.create', compact('warehouses'));
        } catch (\Exception $e) {
            // Log the error and return back with an error message
            Log::error("Error creating adjustment: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error fetching modifications");
        }
    }

    /**
     * Store a new adjustment record.
     * Validates request data, passes it to the service layer, and redirects to the index page.
     * Logs any errors encountered during the process.
     *
     * @param AdjustmentRequest $request The incoming validated request data.
     * @return RedirectResponse Redirects to the index page with a success or error message.
     */
    public function store(AdjustmentRequest $request): RedirectResponse
    {
        try {
            // Validate request data and pass it to the service for storage
            $response = $this->adjustmentService->storeAdjustment($request->validated());

            // Redirect to the adjustment index page with success message
            return redirect()->route('qty_adjustment.index')->with('message', $response['message']);
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error("Error storing adjustment: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error store");
        }
    }

    /**
     * Show the edit view for a specific adjustment.
     * Fetches adjustment data based on ID and returns the edit view.
     * Logs and handles any exceptions.
     *
     * @param int $id The unique identifier of the adjustment to edit.
     * @return View|RedirectResponse The response view or redirection.
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            // Retrieve adjustment details for editing
            $data = $this->adjustmentService->edit($id);

            // Return the edit view with retrieved data
            return view('Tenant.adjustment.edit1', $data);
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error("Error editing adjustment: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error edit");
        }
    }

    /**
     * Update an existing adjustment.
     * Validates incoming request, updates the adjustment in the service layer, and redirects.
     * Logs errors encountered during execution.
     *
     * @param AdjustmentRequest $request The incoming validated request data.
     * @param int $id The unique identifier of the adjustment to update.
     * @return RedirectResponse Redirects to the index page with a success or error message.
     */
    public function update(AdjustmentRequest $request, $id): RedirectResponse
    {
        try {
            // Validate request data and pass it to the service for updating
            $this->adjustmentService->updateAdjustment($request->validated(), $id);

            // Redirect to the index page with success message
            return redirect()->route('qty_adjustment.index')->with('message', 'Updating successfully');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error("Error updating adjustment: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error update");
        }
    }

    /**
     * Delete multiple adjustments based on selected IDs.
     * Processes the deletion request and returns a JSON response.
     * Logs errors encountered during execution.
     *
     * @param Request $request The request containing an array of adjustment IDs to delete.
     * @return JsonResponse|RedirectResponse The JSON response indicating success or failure.
     */
    public function deleteBySelection(Request $request): JsonResponse|RedirectResponse
    {
        try {
            // Process bulk deletion through the service layer
            $response = $this->adjustmentService->deleteBySelection($request->adjustmentIdArray);

            // Return JSON response indicating success
            return response()->json($response);
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error("Error deleting selected adjustments: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error deleteBySelection");
        }
    }

    /**
     * Delete a single adjustment by its ID.
     * Passes the ID to the service layer for deletion and redirects to the adjustment index.
     * Logs errors encountered during execution.
     *
     * @param int $id The unique identifier of the adjustment to delete.
     * @return RedirectResponse Redirects back with success or error message.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Process the deletion in the service layer
            $response = $this->adjustmentService->destroy($id);

            // Redirect to the index page with success message
            return redirect('qty_adjustment')->with('not_permitted', 'Data deleted successfully');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            Log::error("Error deleting adjustment: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error deleted");
        }
    }

}
