<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\ReturnUpdateDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\CustomerService;
use App\Services\Tenant\MailService;
use App\Services\Tenant\ProductReturnService;
use App\Services\Tenant\ProductVariantService;
use App\Services\Tenant\ProductWarehouseService;
use App\Services\Tenant\ReturnService;
use App\Services\Tenant\SaleReturnService;
use App\Services\Tenant\SalesService;
use App\Services\Tenant\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Warehouse;
use App\Models\Biller;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use DB;
use App\Models\Returns;
use App\Models\Account;
use App\Models\ProductReturn;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\Product_Sale;
use App\Models\Currency;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\ReturnDetails;
use Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\MailSetting;
use App\Traits\MailInfo;
use App\Traits\StaffAccess;
use App\Traits\TenantInfo;

class ReturnController extends Controller
{
    //use TenantInfo, MailInfo, StaffAccess;


    protected $returnService;
    protected WarehouseService $warehouseService;
    protected ProductReturnService $productReturnService;
    protected SalesService $saleService;
    protected $saleReturnService;
    protected ProductWarehouseService $productWarehouseService;
    protected ProductVariantService $productVariantService;
    protected CustomerService $customerService;
    protected MailService $emailService;



    public function __construct(
        ReturnService $returnService,
        WarehouseService $warehouseService,
        ProductReturnService $productReturnService,
        SalesService $saleService,
        SaleReturnService $saleReturnService,
        ProductWarehouseService $productWarehouseService,
        ProductVariantService $productVariantService,
        CustomerService $customerService,
        MailService $emailService
    )
    {
        $this->returnService = $returnService;
        $this->warehouseService = $warehouseService;
        $this->productReturnService = $productReturnService;
        $this->saleService = $saleService;
        $this->saleReturnService = $saleReturnService;
        $this->productWarehouseService = $productWarehouseService;
        $this->productVariantService = $productVariantService;
        $this->customerService = $customerService;
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $warehouse_id = $request->input('warehouse_id', 0);
        $starting_date = $request->input('starting_date', Carbon::now()->subYear()->toDateString());
        $ending_date = $request->input('ending_date', Carbon::now()->toDateString());

        // جلب بيانات المستودعات
        $lims_warehouse_list = $this->warehouseService->getWarehouses();

        // جلب بيانات العوائد
        $returnsData = $this->returnService->getReturnsData($warehouse_id, $starting_date, $ending_date);

        return view('Tenant.return.index', compact('returnsData', 'lims_warehouse_list', 'starting_date', 'ending_date', 'warehouse_id'));
    }

    public function productReturnData($id)
    {
        try {
            return response()->json([
                'products' => $this->productReturnService->getProductReturnData($id)
            ]);
        } catch (\Exception $e) {
            Log::error("Error in return productReturnData: " . $e->getMessage());
            return "Error";
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $this->returnService->getSaleData($request->input('reference_no'));

            if (!$data) {
                return redirect()->back()->with('not_permitted', 'This reference either does not exist or status not completed!');
            }

            return view('Tenant.return.create', $data);

        } catch (\Exception $e) {
            Log::error("Error in Return create: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while fetch Return create data. Try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $document = $request->file('document');
            $return = $this->saleReturnService->processSaleReturn($request, $document);

            return redirect('return-sale')->with('message', "Return Stored Successfully");
        } catch (\Exception $e) {
            Log::error("Error in Return store: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while store Return data. Try again.');
        }
    }

    public function edit(int $id)
    {
        try {
            $data = $this->returnService->getReturnDetails($id);
            return view('Tenant.return.edit', $data);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetch Return data Edit. Try again.');
        }
    }

    public function getCustomerGroup($id)
    {
        try {
            $percentage = $this->customerService->getCustomerGroup($id);
            return response()->json($percentage);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function getProduct(int $warehouseId)
    {
        try {
            $productData = $this->productWarehouseService->getProductsByWarehouseWith($warehouseId);
            return response()->json($productData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function limsProductSearch(Request $request): JsonResponse
    {
        try {
            $productData = $this->productVariantService->searchProduct($request->input('data'));
            return response()->json($productData->toArray(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function sendMail(Request $request)
    {
        try {
            $message = $this->emailService->sendReturnEmail($request->input('return_id'));

            return redirect()->back()->with('message', $message);
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while sending email. Try again.');
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $dto = ReturnUpdateDTO::fromRequest($request, $id);
            $success = $this->returnService->update($dto);

            return redirect('return-sale')->with('message', "Return Updated Successfully");

        } catch (\Exception $e) {
            return back()->with('not_permitted', $e->getMessage() . '. Try again.');
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $returnIds = $request->input('returnIdArray');
            $this->returnService->deleteBySelection($returnIds);
            return response()->json("Return deleted successfully!");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->returnService->deleteReturn((int) $id);
            return redirect('return-sale')->with('message', 'Return deleted successfully!');
        } catch (\Exception $e) {
            return redirect('return-sale')->with('not_permitted', 'An error occurred while deleting Return data. Try again. ');
        }
    }

}
