<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ProductRequest;
use App\Http\Requests\Tenant\ProductUpdateRequest;
use App\Http\Requests\Tenant\SearchProductRequest;
use App\Imports\ProductImport;
use App\Services\Tenant\ImportService;
use App\Services\Tenant\ProductHistoryService;
use App\Services\Tenant\ProductSearchService;
use App\Services\Tenant\ProductService;
use App\Services\Tenant\UnitService;
use App\Services\Tenant\WarehouseService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\CacheForget;

class ProductController extends Controller
{
    use CacheForget;

    protected WarehouseService $warehouseService;
    protected ProductService $productService;
    protected ProductSearchService $productSearchService;
    protected ProductHistoryService $historyService;
    protected UnitService $unitService;

    public function __construct(
        ProductSearchService $productSearchService,
        ProductService $productService,
        WarehouseService $warehouseService,
        ProductHistoryService $historyService,
        UnitService $unitService
    )
    {
        $this->productService = $productService;
        $this->productSearchService = $productSearchService;
        $this->warehouseService = $warehouseService;
        $this->historyService = $historyService;
        $this->unitService = $unitService;
    }

    /** ✅  Show product */
    public function index(Request $request)
    {
        // Get the warehouse_id from the request, or set the default value to 0
        $warehouse_id = $request->input('warehouse_id', 0);
        $data = $this->productService->index($warehouse_id);

        return view('Tenant.product.index', $data);

    }

    /** ✅  Create product  */
    public function create()
    {
        $data = $this->productService->getProductCreationData();
        return view('Tenant.product.create', $data);
    }

    /** ✅  Generate Code for product */
    public function generateCode(Request $request)
    {
        $barcode_symbology = $request->input('barcode_symbology');

        // توليد رقم عشوائي دائمًا بناءً على UUID
        $uniqueNumber = str_replace('-', '', Str::uuid()); // إزالة الـ "-" من الـ UUID
        $uniqueNumber = preg_replace('/[^0-9]/', '', $uniqueNumber); // الاحتفاظ فقط بالأرقام

        // التأكد من أن الكود لا يتجاوز الطول المطلوب
        if ($barcode_symbology == 'UPCA') {
            $code = substr($uniqueNumber, 0, 11);
        } elseif ($barcode_symbology == 'EAN8') {
            $code = substr($uniqueNumber, 0, 7);
        } elseif ($barcode_symbology == 'EAN13') {
            $code = substr($uniqueNumber, 0, 12);
        } else {
            $code = substr($uniqueNumber, 0, 10); // كود افتراضي بطول 10 أرقام
        }

        return response()->json(['code' => $code]);

    }

    /** ✅  Store product */
    public function store(ProductRequest $request)
    {
        try {

            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء المنتج بنجاح',
                'product' => $product,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400); // كود 400 يشير إلى طلب غير صحيح
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.',
            ], 500); // كود 500 يشير إلى خطأ في السيرفر
        }
    }


    /** ✅  start: Show product history */
    public function history(Request $request)
    {
        $warehouse_id = $request->input('warehouse_id', 0);
        $starting_date = $request->input('starting_date', now()->subYear()->format('Y-m-d'));
        $ending_date = $request->input('ending_date', now()->format('Y-m-d'));
        $product_id = $request->input('product_id');

        // استدعاء بيانات المنتج والقائمة من الـ Service
        $product_data = $this->productService->getProductData($product_id);
        $lims_warehouse_list = $this->warehouseService->getWarehouses();

        // استدعاء بيانات المبيعات والمشتريات من الـ Service
        $historyData = $this->productService->getSalesAndPurchasesHistory($product_id, $warehouse_id, $starting_date, $ending_date);

        return view('Tenant.product.history', array_merge([
            'starting_date' => $starting_date,
            'ending_date' => $ending_date,
            'warehouse_id' => $warehouse_id,
            'product_id' => $product_id,
            'product_data' => $product_data,
            'lims_warehouse_list' => $lims_warehouse_list
        ], $historyData));
    }

    public function saleReturnHistoryData(Request $request): JsonResponse
    {
        return $this->historyService->getSaleReturnHistoryData($request);
    }

    public function purchaseReturnHistoryData(Request $request): JsonResponse
    {
        return $this->historyService->getPurchaseReturnHistoryData($request);
    }
    /** ✅ end: Show product history */


    /** searching for the product */
    public function search(Request $request): JsonResponse
    {
        try {
            $product = $this->productSearchService->searchProduct($request->input('data'));
            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not exist'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while searching for the product.'], 500);
        }
    }

    /**   fetching sales units  */
    public function saleUnit(int $id): JsonResponse
    {
        try {
            $units = $this->unitService->getSaleUnits($id);
            return response()->json($units, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching sales units.'], 500);
        }
    }

    /**   fetching product data  */
    public function getData(int $id, ?int $variant_id): JsonResponse
    {
        try {
            $data = $this->productService->getProductDataWithVariant($id, $variant_id);
            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not exist'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching product data.'], 500);
        }
    }

    /**   print product data Barcode  */
    public function printBarcode(SearchProductRequest $request)
    {
        $productCode = trim(explode("(", $request->validated()['data'])[0]);
        if($request->input('data')) {
            $preLoadedproducts =  $this->productSearchService->searchByCode($productCode);
        }
        else
            $preLoadedproducts = [];

        $product_list_without_variant = $this->productService->getProductsWithoutVariant();
        $product_list_with_variant = $this->productService->getProductsWithVariant();

        return view('Tenant.product.print_barcode', compact('product_list_without_variant', 'product_list_with_variant', 'preLoadedproducts'));
    }

    /** Set all products to "In Stock" */
    public function allProductInStock()
    {
        try {
            $this->productService->setAllProductsInStock();
            return back()->with('success', 'All products have been successfully set to "In Stock".!');
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('not_permitted', 'Please install the ecommerce addon!');
        } catch (\Exception $e) {
            Log::error("Error updating inventory: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating products.');
        }
    }

    /** ✅ Show all products on the online store */
    public function showAllProductOnline()
    {
        try {
            $this->productService->showAllProductsOnline();
            return back()->with('success', 'All products are displayed on the online store.!');
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('not_permitted', 'Please install the ecommerce addon!');
        } catch (\Exception $e) {
            Log::error("Error updating inventory: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating products');
        }
    }

    /** ✅ Delete multiple products at once */
    public function deleteBySelection(Request $request)
    {
        try {
            $productIds = $request->input('productIdArray', []);
            if (empty($productIds)) {
                return response()->json(['message' => 'No product selected'], 400);
            }

            $this->productService->deleteBySelection($productIds);

            return response()->json(['message' => 'Products have been successfully removed.!']);
        } catch (\Exception $e) {
            Log::error("Error deleting products: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting products..'], 500);
        }
    }

    /** ✅ Delete a specific product */
    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            return redirect()->route('products.index')->with('success', 'The product has been successfully removed.!');
        } catch (\Exception $e) {
            Log::error("An error occurred while deleting the product.: " . $e->getMessage());
            return redirect()->route('products.index')->with('error', 'An error occurred while deleting the product..');
        }
    }

    /** ✅ Import products from file */
    public function importProduct(Request $request)
    {
        try {
            $importService = app(ImportService::class);
            $importService->import(ProductImport::class, $request->file('file'));
            return redirect()->back()->with('import_message', __('The data has been imported successfully and will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /** ✅ Edit product */
    public function edit($id)
    {
        try {
            $data = $this->productService->getEditData($id);
            return view('Tenant.product.edit', $data);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('products.index')->withErrors('Product not exist.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('products.index')->withErrors('An error occurred while get data.');
        }
    }

    /** ✅ Update product */
    public function updateProduct(ProductUpdateRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($request->validated());

            return response()->json(['edit_message' => 'تم تحديث المنتج بنجاح', 'product' => $product]);
        } catch (\Throwable $e) {
            Log::error("Error updating 3232: " . $e->getMessage());
            return response()->json(['edit_message' => 'فشل التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    public function limsProductSearch(SearchProductRequest $request): JsonResponse
    {
        $productCode = trim(explode("(", $request->validated()['data'])[0]);

        $products = $this->productSearchService->searchByCode($productCode);

        return response()->json($products);
    }


















    /*public function checkBatchAvailability($product_id, $batch_no, $warehouse_id)
    {
        $product_batch_data = ProductBatch::where([
            ['product_id', $product_id],
            ['batch_no', $batch_no]
        ])->first();
        if($product_batch_data) {
            $product_warehouse_data = Product_Warehouse::select('qty')
                ->where([
                    ['product_batch_id', $product_batch_data->id],
                    ['warehouse_id', $warehouse_id]
                ])->first();
            if($product_warehouse_data) {
                $data['qty'] = $product_warehouse_data->qty;
                $data['product_batch_id'] = $product_batch_data->id;
                $data['expired_date'] = date(config('date_format'), strtotime($product_batch_data->expired_date));
                $data['message'] = 'ok';
            }
            else {
                $data['qty'] = 0;
                $data['message'] = 'This Batch does not exist in the selected warehouse!';
            }
        }
        else {
            $data['message'] = 'Wrong Batch Number!';
        }
        return $data;
    }
    public function productWarehouseData($id)
    {
        $warehouse = [];
        $qty = [];
        $batch = [];
        $expired_date = [];
        $imei_number = [];
        $warehouse_name = [];
        $variant_name = [];
        $variant_qty = [];
        $product_warehouse = [];
        $product_variant_warehouse = [];
        $lims_product_data = Product::select('id', 'is_variant')->find($id);
        if($lims_product_data->is_variant) {
            $lims_product_variant_warehouse_data = Product_Warehouse::where('product_id', $lims_product_data->id)->orderBy('warehouse_id')->get();
            $lims_product_warehouse_data = Product_Warehouse::select('warehouse_id', DB::raw('sum(qty) as qty'))->where('product_id', $id)->groupBy('warehouse_id')->get();
            foreach ($lims_product_variant_warehouse_data as $key => $product_variant_warehouse_data) {
                $lims_warehouse_data = Warehouse::find($product_variant_warehouse_data->warehouse_id);
                $lims_variant_data = Variant::find($product_variant_warehouse_data->variant_id);
                $warehouse_name[] = $lims_warehouse_data->name;
                $variant_name[] = $lims_variant_data->name;
                $variant_qty[] = $product_variant_warehouse_data->qty;
            }
        }
        else {
            $lims_product_warehouse_data = Product_Warehouse::where('product_id', $id)->orderBy('warehouse_id', 'asc')->get();
        }
        foreach ($lims_product_warehouse_data as $key => $product_warehouse_data) {
            $lims_warehouse_data = Warehouse::find($product_warehouse_data->warehouse_id);
            if($product_warehouse_data->product_batch_id) {
                $product_batch_data = ProductBatch::select('batch_no', 'expired_date')->find($product_warehouse_data->product_batch_id);
                $batch_no = $product_batch_data->batch_no;
                $expiredDate = date(config('date_format'), strtotime($product_batch_data->expired_date));
            }
            else {
                $batch_no = 'N/A';
                $expiredDate = 'N/A';
            }
            $warehouse[] = $lims_warehouse_data->name;
            $batch[] = $batch_no;
            $expired_date[] = $expiredDate;
            $qty[] = $product_warehouse_data->qty;
            if($product_warehouse_data->imei_number)
                $imei_number[] = $product_warehouse_data->imei_number;
            else
                $imei_number[] = 'N/A';
        }

        $product_warehouse = [$warehouse, $qty, $batch, $expired_date, $imei_number];
        $product_variant_warehouse = [$warehouse_name, $variant_name, $variant_qty];
        return ['product_warehouse' => $product_warehouse, 'product_variant_warehouse' => $product_variant_warehouse];
    }
    public function variantData($id)
    {
        if(Auth::user()->role_id > 2) {
            return ProductVariant::join('variants', 'product_variants.variant_id', '=', 'variants.id')
                ->join('product_warehouse', function($join) {
                    $join->on('product_variants.product_id', '=', 'product_warehouse.product_id');
                    $join->on('product_variants.variant_id', '=', 'product_warehouse.variant_id');
                })
                ->select('variants.name', 'product_variants.item_code', 'product_variants.additional_cost', 'product_variants.additional_price', 'product_warehouse.qty')
                ->where([
                    ['product_warehouse.product_id', $id],
                    ['product_warehouse.warehouse_id', Auth::user()->warehouse_id]
                ])
                ->orderBy('product_variants.position')
                ->get();
        }
        else {
            return ProductVariant::join('variants', 'product_variants.variant_id', '=', 'variants.id')
                ->select('variants.name', 'product_variants.item_code', 'product_variants.additional_cost', 'product_variants.additional_price', 'product_variants.qty')
                ->orderBy('product_variants.position')
                ->where('product_id', $id)
                ->get();
        }
    }*/

}
