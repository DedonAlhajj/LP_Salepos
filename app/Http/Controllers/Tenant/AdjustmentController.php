<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AccountRequest;
use App\Services\Tenant\AdjustmentService;
use App\Services\Tenant\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdjustmentController extends Controller
{

    private $adjustmentService;
    protected $productService;

    public function __construct(AdjustmentService $adjustmentService,ProductService $productService)
    {
        $this->adjustmentService = $adjustmentService;
        $this->productService = $productService;

    }

    /** Show Adjustment*/
    public function index(Request $request)
    {
        try {
            $adjustments = $this->adjustmentService->getAllAdjustments();

            return view('Tenant.adjustment.index', compact('adjustments'));
        } catch (\Exception $e) {
            Log::error("Error fetching modifications: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error fetching modifications");
        }
    }

    /** Show Products By Warehouse */
    public function getProduct(int $id): JsonResponse
    {
        try {
            $productData = $this->productService->getProductsByWarehouse($id);
            return response()->json($productData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data.', 'details' => $e->getMessage()], 500);
        }
    }

    /** Find product by code */
    public function limsProductSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|string|max:255'
        ]);

        $result = $this->productService->searchProduct($validated['data']);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json($result);
    }

    /** Show create view */
    public function create()
    {
        try {
            $warehouses = $this->adjustmentService->getCreateData();
            return view('Tenant.adjustment.create', compact('warehouses'));
        } catch (\Exception $e) {
            Log::error("Error create : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error fetching modifications");
        }

    }

    /** Store Adjustment */
    public function store(AccountRequest $request)
    {
        try {
            $response = $this->adjustmentService->storeAdjustment($request->validated());
            return redirect()->route('qty_adjustment.index')->with('message', $response['message']);
        } catch (\Exception $e) {
            Log::error("Error store : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error store");
        }

    }

    /** Show edit view */
    public function edit($id)
    {
        try {
            $data = $this->adjustmentService->edit($id);
            return view('Tenant.adjustment.edit1', $data);
        } catch (\Exception $e) {
            Log::error("Error edit : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error edit");
        }
    }

    /** update Adjustment */
    public function update(AccountRequest $request, $id)
    {
        try {
            $this->adjustmentService->updateAdjustment($request->validated(), $id);
            return redirect()->route('qty_adjustment.index')->with('message', 'Updating successfully');
        } catch (\Exception $e) {
            Log::error("Error update : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error update");
        }
    }

    /** Delete Multi Adjustment */
    public function deleteBySelection(Request $request)
    {
        try {
            $response = $this->adjustmentService->deleteBySelection($request->adjustmentIdArray);
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error("Error deleteBySelection : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error deleteBySelection");
        }
    }

    /** Delete Adjustment */
    public function destroy($id)
    {
        try {
            $response = $this->adjustmentService->destroy($id);
            return redirect('qty_adjustment')->with('not_permitted', 'Data deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error deleted : " . $e->getMessage());
            return redirect()->back()->with('not_permitted', "Error deleted");
        }
    }

}
