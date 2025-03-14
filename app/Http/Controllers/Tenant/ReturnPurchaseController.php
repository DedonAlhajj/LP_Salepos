<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\PurchaseReturnDTO;
use App\DTOs\ReturnPurchaseEditDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\MailService;
use App\Services\Tenant\ProductReturnService;
use App\Services\Tenant\ProductSearchService;
use App\Services\Tenant\ReturnPurchaseService;
use App\Services\Tenant\WarehouseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReturnPurchaseController extends Controller
{

    protected WarehouseService $warehouseService;
    protected ReturnPurchaseService $returnPurchaseService;
    protected MailService $emailService;
    protected ProductReturnService $productReturnService;
    protected ProductSearchService $productSearchService;




    public function __construct(
        ReturnPurchaseService $returnPurchaseService,
        WarehouseService $warehouseService,
        MailService $emailService,
        ProductReturnService $productReturnService,
        ProductSearchService $productSearchService
    )
    {
        $this->returnPurchaseService = $returnPurchaseService;
        $this->warehouseService = $warehouseService;
        $this->emailService = $emailService;
        $this->productReturnService = $productReturnService;
        $this->productSearchService = $productSearchService;
    }
    public function index(Request $request)
    {
        try {

            $warehouse_id  = $request->input('warehouse_id', 0);
            $starting_date = $request->input('starting_date', now()->subYears(2)->format('Y-m-d'));
            $ending_date   = $request->input('ending_date', now()->format('Y-m-d'));

            $lims_warehouse_list = $this->warehouseService->getWarehouses();
            $returnPurchases     = $this->returnPurchaseService->getReturnPurchases($warehouse_id, $starting_date, $ending_date);
            $data                = $this->returnPurchaseService->formatReturnPurchases($returnPurchases);

            return view('Tenant.return_purchase.index', compact('starting_date',
                'ending_date', 'warehouse_id', 'lims_warehouse_list', 'data'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetch Return data. Try again.');
        }
    }

    public function productReturnData(int $id): JsonResponse
    {
        try {
            $productReturnData = $this->productReturnService->getProductReturnPurchaseData($id);
            return response()->json($productReturnData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $this->returnPurchaseService->getCreateData($request->input('reference_no'));
            return view('Tenant.return_purchase.create', $data);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('not_permitted', 'Reference number does not exist!');
        }
    }

    public function store(Request $request)
    {
        try {

            $dto = PurchaseReturnDTO::fromRequest($request);
            $this->returnPurchaseService->createReturn($dto);
            return redirect('return-purchase')->with('message', "Purchase return processed successfully");
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while store Return data. Try again.');
        }
    }

    public function edit($id)
    {
        //dd($this->productReturnService->getProductsByWarehouse(1));
        //dd($this->productSearchService->searchProduct("02456392 (10203743)"));
        try {
            $data = $this->returnPurchaseService->getReturnData($id);
            return view('Tenant.return_purchase.edit', $data);
        } catch (\Exception $e) {
            Log::error("Error in Return Purchase edit: " . $e->getMessage());
            return back()->withErrors("Error getting Return Purchase data.");
        }
    }

    public function getProduct($id)
    {
        try {
            $productReturnData = $this->productReturnService->getProductsByWarehouse($id);
            return response()->json($productReturnData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function limsProductSearch(Request $request): JsonResponse
    {
        try {
            $productData = $this->productSearchService->searchProduct($request->input('data'));
            return response()->json($productData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $data = ReturnPurchaseEditDTO::fromRequest($request, $id);
            $this->returnPurchaseService->update($data);

            return redirect()->route('return-purchase.index')->with('message', 'Purchase return processed successfully');

        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while update Return data. Try again.');
        }
    }

    public function deleteBySelection(Request $request)
    {
        $returnIds = $request->input('returnIdArray');
        try {
            $message = $this->returnPurchaseService->deleteBySelection($returnIds);
            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['not_permitted' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // استخدام الـ Service لحذف بيانات المرتجع
            $this->returnPurchaseService->destroy($id);

            return redirect('return-purchase')->with('message', 'Data deleted successfully');
        } catch (\Exception $e) {
            return redirect('return-purchase')->with('not_permitted', 'Failed to delete data');
        }
    }
}
