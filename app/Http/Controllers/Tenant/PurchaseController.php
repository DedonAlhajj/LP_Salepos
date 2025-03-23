<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Imports\PurchaseImport;
use App\Services\Tenant\AccountService;
use App\Services\Tenant\PaymentService;
use App\Services\Tenant\PosSettingService;
use App\Services\Tenant\ProductPurchaseService;
use App\Services\Tenant\ProductSearchService;
use App\Services\Tenant\PurchaseService;
use App\Services\Tenant\WarehouseService;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class PurchaseController extends Controller
{


    private $purchaseService;
    private $accountService;
    private $posSettingService;
    private  $warehouseService;
    private $productPurchaseService;
    private $productSearchService;
    private $paymentService;

    public function __construct(
        PurchaseService        $purchaseService,
        AccountService $accountService,
        PosSettingService      $posSettingService,
        WarehouseService       $warehouseService,
        ProductPurchaseService $productPurchaseService,
        ProductSearchService   $productSearchService,
        PaymentService         $paymentService,
    )
    {
        $this->purchaseService = $purchaseService;
        $this->accountService = $accountService;
        $this->posSettingService = $posSettingService;
        $this->warehouseService = $warehouseService;
        $this->productPurchaseService = $productPurchaseService;
        $this->productSearchService = $productSearchService;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        try {
            // Get filters from request with default values
            $filters = $this->purchaseService->getFilters($request);

            // Get purchase data via Service with filters
            $purchases = $this->purchaseService->getPurchases($filters);

            // Get data for stores, accounts, and POS settings
            $lims_warehouse_list = $this->warehouseService->getWarehouses();
            $lims_account_list = $this->accountService->getActiveAccounts();
            $lims_pos_setting_data = $this->posSettingService->getStripePublicKey();

            return view('Tenant.purchase.index', compact(
                'lims_account_list',
                'lims_warehouse_list',
                'filters',
                'purchases',
                'lims_pos_setting_data'
            ));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetching data. Try again.');
        }
    }

    public function productPurchaseData($id)
    {
        try {
            $productPurchaseData = $this->productPurchaseService->getProductPurchaseData($id);

            return response()->json($productPurchaseData);
        } catch (\Exception $e) {
            return response()->json("Something is wrong!");
        }
    }

    public function create()
    {

        try {
            $purchaseData = $this->purchaseService->getAllPurchaseData(Auth::user());

            return view('Tenant.purchase.create', $purchaseData);
        } catch (\Exception $e) {
            Log::error("Error in Purchase Create: " . $e->getMessage());
            return back()->with('error', 'Failed to load purchase page.');
        }
    }

    public function limsProductSearch(Request $request)
    {
        $productDTO = $this->productSearchService->searchProductByCodeOrVariant($request->input('data'));

        if (!$productDTO) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($productDTO->toArray());
    }

    public function store(Request $request)
    {
     //   dd($request);
        try {
            $this->purchaseService->storePurchase($request->all());
            return redirect()->route('purchases.index')->with('message', 'Purchase created successfully');
        } catch (\Exception $e) {
            //Log::error("Error in Purchase store: " . $e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function purchaseByCsv()
    {
        try {
            $purchaseData = $this->purchaseService->purchaseByCsv();
            return view('Tenant.purchase.import', $purchaseData);
        } catch (\Exception $e) {
            Log::error("Error in Purchase Create: " . $e->getMessage());
            return back()->with('not_permitted', 'Failed to load purchase page.');
        }

    }

    public function importPurchase(Request $request)
    {
        try {
            $file = $request->file('file');

            // تمرير بيانات الطلب عند إنشاء `PurchaseImport`
            Excel::import(new PurchaseImport($request->all()), $file);

            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Purchase $Purchase)
    {
        try {
            $purchaseData = $this->purchaseService->getAllPurchaseDataEdit($Purchase);
            return view('Tenant.purchase.edit', $purchaseData);
        } catch (\Exception $e) {
            Log::error("Error in Purchase Create: " . $e->getMessage());
            return back()->with('error', 'Failed to load purchase page.');
        }

    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document');
        }

        $result = $this->purchaseService->updatePurchase($data, $id);

        if ($result['success']) {
            return redirect('purchases')->with('message', $result['message']);
        } else {
            return redirect()->back()->withErrors($result['message']);
        }
    }

    public function duplicate($Purchase)
    {

        try {
            $purchaseData = $this->purchaseService->duplicate($Purchase);
            return view('Tenant.purchase.duplicate', $purchaseData);
        } catch (\Exception $e) {
            Log::error("Error in Purchase (duplicate) : " . $e->getMessage());
            return back()->with('error', 'Failed to load purchase page.');
        }

    }

    public function addPayment(Request $request)
    {
        try {
            $this->paymentService->processPayment($request->all());
            return redirect()->route('purchases.index')->with('message', 'Payment created successfully');
        } catch (\Exception $e) {
            Log::error("Error in Purchase payment: " . $e->getMessage());
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function getPayment($id)
    {
        try {
            $payments = $this->paymentService->getPayment($id);
            return response()->json($payments);
        } catch (\Exception $e) {
            Log::error("Error in Purchase get payment: " . $e->getMessage());
            return response()->json("Error in Purchase get payment:");
        }
    }

    public function updatePayment(Request $request)
    {
        try {
            $this->paymentService->updatePayment($request->all());
            return redirect('purchases')->with('message', 'Payment updated successfully');
        } catch (\Exception $e) {
            Log::error("Error in Purchase updata payment: " . $e->getMessage());
            return redirect('purchases')->with('error', $e->getMessage());
        }
    }

    public function deletePayment(Request $request)
    {
        try {
            $this->paymentService->deletePayment($request->id);
            return redirect('purchases')->with('message', 'Payment deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error in Purchase delete payment: " . $e->getMessage());
            return redirect('purchases')->with('error', $e->getMessage());
        }

    }

    public function destroy($id)
    {
        try {
            $this->purchaseService->deletePurchase($id);
            return redirect('purchases')->with('message', 'Purchase deleted successfully');
        } catch (\Exception $e) {
            Log::error("Error in Purchase delete payment: " . $e->getMessage());
            return redirect('purchases')->with('error', $e->getMessage());
        }
    }


}
