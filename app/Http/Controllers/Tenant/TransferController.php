<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\TransferData;
use App\Http\Controllers\Controller;
use App\Imports\PurchaseImport;
use App\Imports\TransferImport;
use App\Mail\TransferDetails;
use App\Services\Tenant\ProductService;
use App\Services\Tenant\ProductTransferService;
use App\Services\Tenant\ProductWarehouseService;
use App\Services\Tenant\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Transfer;
use App\Models\ProductTransfer;
use App\Models\ProductVariant;
use App\Models\ProductBatch;
use App\Models\MailSetting;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{

    protected TransferService $transferService;
    protected ProductTransferService $productTransferService;
    protected ProductWarehouseService $productWarehouseService;
    protected ProductService $productService;

    public function __construct(
        TransferService $transferService,
        ProductTransferService $productTransferService,
        ProductWarehouseService $productWarehouseService,
        ProductService $productService)
    {
        $this->transferService = $transferService;
        $this->productTransferService = $productTransferService;
        $this->productWarehouseService = $productWarehouseService;
        $this->productService = $productService;
    }
    public function index(Request $request)
    {
        try {
            $data = $this->transferService->getFilters($request);

            // جلب التحويلات باستخدام خدمة TransferService
            $transfers = $this->transferService->getTransfers($data);

            return view('Tenant.transfer.index', compact('data', 'transfers'));
        } catch (\Exception $e) {
            Log::error("Error in transfer index: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }

    }

    public function productTransferData(int $id): JsonResponse
    {
        try {
            $data = $this->productTransferService->getProductTransferData($id);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error("Error in transfer product TransferData: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function create()
    {
        try {
            $data = $this->transferService->getCreateData();
            return view('Tenant.transfer.create', $data);
        } catch (\Exception $e) {
            Log::error("Error in transfer index: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }

    public function getProduct(int $id): JsonResponse
    {
        try {
            return response()->json($this->productWarehouseService->getProductsByWarehouse($id));
        } catch (\Exception $e) {
            Log::error("Error in transfer getProduct: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function limsProductSearch(Request $request)
    {
        try {
            return response()->json($this->productService->searchProductTransfer($request->input('data')));
        } catch (\Exception $e) {
            Log::error("Error in transfer limsProductSearch: " . $e->getMessage());
            return response()->json("Error");
        }
    }

    public function store(Request $request)
    {
        try {
            // تعيين البيانات من الـRequest إلى TransferData
            $data = TransferData::fromRequest($request->all());
            $document = $request->file('document');  // إذا كان هناك مستند

            // استدعاء الدالة في TransferService
            $message = $this->transferService->storeTransferData($data, $document);

            return redirect('transfers')->with('message', $message);
        } catch (\Exception $e) {
            Log::error("Error in transfer store: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }


    public function transferByCsv()
    {
        try {
            $data = $this->transferService->getCreateData();
            return view('Tenant.transfer.import', $data);
        } catch (\Exception $e) {
            Log::error("Error in transfer transferByCsv: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer data. Try again.');
        }
    }

    public function importTransfer(Request $request)
    {
        try {
            $transferData = $request->except('file');
            $transferData['reference_no'] = 'tr-' . now()->format('Ymd-His');
            $transferData['user_id'] = auth()->id();

            // التحقق من صحة البيانات الأساسية للتحويل
            Validator::make($transferData, [
                'from_warehouse_id' => 'required|integer|exists:warehouses,id',
                'to_warehouse_id'   => 'required|integer|exists:warehouses,id',
                'status'            => 'required|integer|in:1,2,3',
                'shipping_cost'     => 'required|numeric|min:0',
                'total_qty'         => 'nullable|numeric|min:0',
                'total_tax'         => 'nullable|numeric|min:0',
                'total_cost'        => 'nullable|numeric|min:0',
                'item'              => 'nullable|numeric|min:0',
                'grand_total'       => 'nullable|numeric|min:0',
                'note'              => 'nullable|string',
            ])->validate();

            Excel::import(new TransferImport($transferData), $request->file('file'));
            return redirect('transfers')->with('message', __('Transfer imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }


    public function edit($id)
    {
        try {
            $transferDetails = $this->transferService->getTransferDetails($id);

            return view('Tenant.transfer.edit', [
                'transfer' => $transferDetails->transfer,
                'warehouses' => $transferDetails->warehouses,
                'products' => $transferDetails->products // يتم تمريرها كمصفوفة عادية
            ]);

        } catch (\Exception $e) {
            Log::error("Error in transfer edit: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch transfer edit data. Try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->transferService->updateTransfer($request, $id);
            return redirect('transfers')->with('message', 'Transfer updated successfully');

        } catch (\Exception $e) {
            Log::error("Error in transfer updated: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while updated transfer. Try again.');
        }
    }

    public function deleteBySelection(Request $request)
    {
        $transferIds = $request->input('transferIdArray');

        try {
            $this->transferService->deleteBySelection($transferIds);
            return response()->json('Transfers deleted successfully!', 200);
        } catch (\Exception $e) {
            Log::error("Error deleting transfers deleteBySelection: " . $e->getMessage());
            return response()->json('Failed to delete transfers.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->transferService->deleteTransfer($id);
            return redirect('transfers')->with('message', 'Transfer deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error deleting destroy: " . $e->getMessage());
            return redirect('transfers')->with('not_permitted', 'Transfer deleted successfully');
        }

    }
}
