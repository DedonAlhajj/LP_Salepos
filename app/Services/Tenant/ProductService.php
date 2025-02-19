<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomFieldValue;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductPurchase;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Variant;
use App\Models\Warehouse;
use App\Models\CustomField;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected SendMailAction $sendMailAction;
    protected WarehouseService $warehouseService;
    protected CustomFieldService $customFieldService;
    protected ProductHistoryService $historyService;



    public function __construct(
        SendMailAction $sendMailAction ,
        WarehouseService $warehouseService,
        CustomFieldService $customFieldService,
        ProductHistoryService $historyService
        )
    {
        $this->sendMailAction = $sendMailAction;
        $this->warehouseService = $warehouseService;
        $this->customFieldService = $customFieldService;
        $this->historyService = $historyService;
    }

    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }

    public function authorizeConfig()
    {
        if(!in_array('ecommerce',explode(',', config('addons')))) {
            throw new AuthorizationException(__('Sorry! Please install the ecommerce addon!.'));
        }

    }

    public function index($warehouse_id = 0)
    {
        return  [
            'warehouses'     => $this->warehouseService->getWarehouses(),
            'warehouse_id'   => $warehouse_id,
            'products'       => $this->getProductsWithCustomFields($warehouse_id),
            'custom_fields'  => $this->customFieldService->getCustomFieldsWithTable('product'),
        ];
    }

    public function getProductsWithCustomFields($warehouse_id)
    {
        return Product::with([
            'category',
            'brand',
            'unit',
            'customFields.customField',
            'tax' // إضافة الضريبة
        ])
            ->withTrashed() // جلب المنتجات المحذوفة باستخدام soft delete
            ->get()
            ->map(function ($product) use ($warehouse_id) {
                return [
                    'id'           => $product->id,
                    'image'        => $product->image,
                    //'image'        => $product->getProductImage($product),
                    'name'         => $product->name,
                    'type'         => $product->type,
                    'product_details'  => preg_replace('/\s+/S', " ", $product->product_details), // إصلاح التفاصيل
                    'code'         => $product->code,
                    'brand'        => $product->brand->title ?? 'N/A',
                    'category'     => $product->category->name ?? 'N/A',
                    'qty'          => $this->getProductQuantity($product, $warehouse_id),
                    'unit'         => $product->unit->unit_name ?? 'N/A',
                    'price'        => $product->price,
                    'cost'         => $product->cost,
                    'stock_worth'  => $this->calculateStockWorth($product),
                    'custom_fields' => $this->customFieldService->getProductCustomFields($product),
                    'tax'          => $product->tax?->name ?? 'N/A',
                    'tax_method'   => $product->tax_method == 1 ? trans('file.Exclusive') : trans('file.Inclusive'),
                    'alert_quantity' => $product->alert_quantity,
                    'product_list' => $product->product_list,
                    'variant_list' => $product->variant_list,
                    'qty_list'     => $product->qty_list,
                    'price_list'   => $product->price_list,
                    'is_variant'   => $product->is_variant,
                ];
            });

    }

    /*** Bring a picture of the product with correct path verification */
    private function getProductImage(Product $product): string
    {
        $product_image = explode(",", $product->image)[0] ?? 'zummXD2dvAtI.png';

        if ($product_image && file_exists(public_path("images/product/small/{$product_image}"))) {
            return asset("images/product/small/{$product_image}");
        }

        return asset("images/product/{$product_image}");
    }

    /** * Calculate product quantity based on warehouse */
    private function getProductQuantity(Product $product, int $warehouse_id = 0): int
    {
        if ($product->type == 'standard') {
            if ($warehouse_id > 0) {
                return $product->warehouses()->where('warehouse_id', $warehouse_id)->sum('qty');
            }
            return $product->warehouses()->sum('qty');
        }

        return $product->qty;
    }

    /** Calculate inventory value based on price and cost */
    private function calculateStockWorth(Product $product): string
    {
        $stockValue = $this->getProductQuantity($product) * $product->price;
        $costValue = $this->getProductQuantity($product) * $product->cost;
        $currency = config('currency');

        return "{$currency} {$stockValue} / {$currency} {$costValue}";
    }


    public function getProductsWithoutVariant()
    {
        return Product::activeStandard()
            ->select('id', 'name', 'code')
            ->whereNull('is_variant')
            ->get();
    }

    /** * جلب قائمة المنتجات التي لها متغيرات (مع Variants) */
    public function getProductsWithVariant()
    {
        return Product::join('product_variants', 'products.id', 'product_variants.product_id')
            ->activeStandard()
            ->whereNotNull('is_variant')
            ->select('products.id', 'products.name', 'product_variants.item_code')
            ->orderBy('position')
            ->get();
    }


    public function getProductCreationData()
    {
        return [
            'products_without_variant' => $this->getProductsWithoutVariant(),
            'products_with_variant' => $this->getProductsWithVariant(),
            'brands' => Brand::all(),
            'categories' => Category::all(),
            'units' => Unit::all(),
            'taxes' => Tax::all(),
            'warehouses' => Warehouse::all(),
            'number_of_products' => Product::count(),
            'custom_fields' => $this->customFieldService->getCustomFields('product'),
        ];
    }


    /** store ***/
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        try {
            // معالجة الفئات المتعددة إن وجدت
            $this->handleVariants($data);

            // معالجة البيانات الخاصة بالـ Custom Fields
            $customFields = CustomField::where([
                ['entity_type', 'product'],
                ['is_table', true]
            ])->get();

            // إنشاء المنتج
            $product = Product::create($data);

            // حفظ الصور والملفات
            $this->handleMedia($product, $data);

            // حفظ الحقول المخصصة
            $this->storeCustomFields($product, $data, $customFields);

            // التعامل مع المخزون الأولي إن وجد
            $this->handleInitialStock($product, $data);

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException('حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage());
        }
    }

    private function handleVariants(array &$data)
    {
        if (isset($data['is_variant'])) {
            $data['variant_option'] = json_encode($data['variant_option']);
            $data['variant_value'] = json_encode($data['variant_value']);
        } else {
            $data['variant_option'] = $data['variant_value'] = null;
        }
    }

    private function handleMedia(Product $product, array &$data)
    {
        if (!empty($data['image'])) {
            foreach ($data['image'] as $image) {
                $product->addMedia($image)->toMediaCollection("product_images");
            }
        }

        if (!empty($data['file'])) {
            $product->addMedia($data['file'])->toMediaCollection("product_files");
        }
    }

    private function storeCustomFields(Product $product, array $data, $customFields)
    {
        foreach ($customFields as $customField) {
            $fieldName = str_replace(' ', '_', strtolower($customField->name));

            if (isset($data[$fieldName])) {
                CustomFieldValue::create([
                    'custom_field_id' => $customField->id,
                    'entity_type' => 'product',
                    'entity_id' => $product->id,
                    'value' => is_array($data[$fieldName]) ? implode(",", $data[$fieldName]) : $data[$fieldName],
                ]);
            }
        }
    }

    private function handleInitialStock(Product $product, array $data)
    {
        if (!isset($data['is_initial_stock']) || isset($data['is_variant']) || isset($data['is_batch'])) {
            return;
        }

        $initialStock = 0;

        foreach ($data['stock_warehouse_id'] as $key => $warehouseId) {
            $stock = $data['stock'][$key];

            if ($stock > 0) {
                PurchaseService::autoPurchase($product, $warehouseId, $stock);
                $initialStock += $stock;
            }
        }

        if ($initialStock > 0) {
            $product->update(['qty' => $product->qty + $initialStock]);
        }
    }

    public function getProductData($product_id)
    {
        return Product::select('name', 'code')->findOrFail($product_id);
    }

    public function getProductWhere(string $product_code)
    {
        return Product::byCode($product_code)->firstOrFail();
    }

    public function getSalesAndPurchasesHistory($product_id, $warehouse_id, $starting_date, $ending_date)
    {
        $salesData = $this->historyService->getSaleHistory($product_id, $warehouse_id, $starting_date, $ending_date);
        $purchasesData = $this->historyService->getPurchaseHistory($product_id, $warehouse_id, $starting_date, $ending_date);
        return compact('salesData', 'purchasesData');
    }

    public function getEditData(int $id): array
    {
        $product = Product::with(['variants' => function ($query) {
            $query->orderBy('position');
        }])->findOrFail($id);

        $product->variant_option = $product->variant_option ? json_decode($product->variant_option, true) : [];
        $product->variant_value = $product->variant_value ? json_decode($product->variant_value, true) : [];

        $related_products = [];
        if ($this->isEcommerceEnabled() && $product->related_products) {
            $related_products = Product::whereIn('id', explode(',', $product->related_products))
                ->select(['id', 'name'])
                ->get();
        }

        return [
            'related_products' => $related_products,
            'lims_product_list_without_variant' => $this->getProductsWithoutVariant(),
            'lims_product_list_with_variant' => $this->getProductsWithVariant(),
            'lims_brand_list' => Brand::select(['id', 'title'])->get(),
            'lims_category_list' => Category::select(['id', 'name'])->get(),
            'lims_unit_list' => Unit::select(['id', 'unit_name'])->get(),
            'lims_tax_list' => Tax::select(['id', 'name'])->get(),
            'lims_product_data' => $product,
            'lims_product_variant_data' => $product->variants,
            'lims_warehouse_list' => Warehouse::select(['id', 'name'])->get(),
            'noOfVariantValue' => 0,
            'custom_fields' => CustomField::where('entity_type', 'product')->get(),
        ];
    }

    private function isEcommerceEnabled(): bool
    {
        return in_array('ecommerce', explode(',', config('addons')));
    }


    /**  Update state */
    public function updateProduct(array $data)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($data['id']);
            $updatedData = collect($data)->except(['image', 'file', 'prev_img', 'product_id', 'variant_id', 'product_qty', 'unit_price'])->toArray();

            $updatedData['name'] = htmlspecialchars(trim($updatedData['name']), ENT_QUOTES);
            //$updatedData['slug'] = isset($data['name']) ? Str::slug($data['name'], '-') : $product->slug;

            $this->handleMedia($product, $data);
            $this->handleProductVariants($product, $data);
            $this->handleProductWarehouses($product, $data);
            $this->updateCustomFields($product, $data);

            $product->update($updatedData);

            DB::commit();
            return $product;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating 77777: " . $e->getMessage());
            throw new \RuntimeException("فشل تحديث المنتج: " . $e->getMessage());
        }
    }

    private function handleProductVariants(Product $product, array &$data)
    {
        if (!isset($data['is_variant'])) {
            $product->variants()->delete();
            return;
        }

        $variants = $data['variant_name'] ?? [];
        foreach ($variants as $index => $variantName) {
            $variant = Variant::firstOrCreate(['name' => $variantName]);

            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'variant_id' => $variant->id],
                [
                    'position' => $index + 1,
                    'item_code' => $data['item_code'][$index] ?? null,
                    'additional_cost' => $data['additional_cost'][$index] ?? 0,
                    'additional_price' => $data['additional_price'][$index] ?? 0,
                ]
            );
        }
    }

    private function handleProductWarehouses(Product $product, array &$data)
    {
        if (!isset($data['is_diffPrice'])) {
            $product->warehouses()->update(['price' => null]);
            return;
        }

        foreach ($data['diff_price'] as $index => $price) {
            Product_Warehouse::updateOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $data['warehouse_id'][$index]],
                ['price' => $price]
            );
        }
    }

    private function updateCustomFields(Product $product, array &$data)
    {
        $customFields = CustomFieldValue::where('entity_type', 'product')->where('entity_id', $product->id)->get();

        foreach ($customFields as $customField) {
            $customField->update(['value' => $data[$customField->customField->name] ?? $customField->value]);
        }
    }
    /**  Update end */


    /** Get product data by id*/
    public function getProductDataWithVariant(int $id, ?int $variant_id)
    {
        if ($variant_id) {
            $data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.name', 'product_variants.item_code')
                ->where([
                    ['products.id', $id],
                    ['product_variants.variant_id', $variant_id]
                ])
                ->firstOrFail();

            $data->code = $data->item_code;
        } else {

            $data = $this->getProductData($id);
        }

        return $data;
    }

    /** Update all products to "In Stock"*/
    /** @throws AuthorizationException */
    public function setAllProductsInStock()
    {
        $this->authorizeConfig();
        return Product::available()->update(['in_stock' => true]);
    }

    /** Show all products for sale online */
    /** @throws AuthorizationException */
    public function showAllProductsOnline()
    {
        $this->authorizeConfig();
        return Product::available()->update(['is_online' => true]);
    }

    /** Delete multiple products at once using SoftDeletes */
    public function deleteBySelection(array $productIds)
    {
        DB::beginTransaction();

        try {
            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                $product->clearMediaCollection('product_images');
            }
            Product::whereIn('id', $productIds)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    /** ✅ Delete a specific product and process the images associated with it */
    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            // Delete images associated with MediaLibrary
            $product->clearMediaCollection('product_images');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function getProductsByWarehouse(int $warehouseId): array
    {
        $products = Product::with([
            'warehouses' => function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            },
            'variants'
        ])->get();

        return $this->formatProductData($products, $warehouseId);
    }

    private function formatProductData(Collection $products, int $warehouseId): array
    {
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_cost = [];

        foreach ($products as $product) {
            foreach ($product->warehouses as $productWarehouse) {
                $product_qty[] = $productWarehouse->qty;
                $product_code[] = $product->is_variant
                    ? $product->variants->first()->item_code ?? ''
                    : $product->code;
                $product_name[] = $product->name;

                $product_cost[] = $this->getProductCost($product->id, $warehouseId, $productWarehouse->variant_id ?? null);
            }
        }

        $product_data = [$product_code, $product_name, $product_qty, $product_cost];
        // ✅ تسجيل البيانات في السجلات (log)
        Log::info('بيانات المنتجات المسترجعة:', [
            'product_code' => $product_code,
            'product_name' => $product_name,
            'product_qty' => $product_qty,
            'product_cost' => $product_cost
        ]);

        return $product_data;
    }

    private function getProductCost(int $productId, int $warehouseId, ?int $variantId = null): float
    {
        $query = ProductPurchase::join('purchases', 'product_purchases.purchase_id', '=', 'purchases.id')
            ->where('product_purchases.product_id', $productId)
            ->where('purchases.warehouse_id', $warehouseId); // التصحيح هنا

        if ($variantId) {
            $query->where('product_purchases.variant_id', $variantId);
        }

        $productPurchase = $query->selectRaw('SUM(product_purchases.qty) AS total_qty, SUM(product_purchases.total) AS total_cost')
            ->first();

        return ($productPurchase && $productPurchase->total_qty > 0)
            ? $productPurchase->total_cost / $productPurchase->total_qty
            : Product::find($productId)->cost ?? 0;
    }


    public function searchProduct(string $searchTerm): array
    {
        try {
            $productCode = explode("(", $searchTerm)[0];
            $productCode = trim($productCode);

            // البحث عن المنتج العادي أو المنتج المتغير
            $product = Product::withVariantCode($productCode)->firstOrFail();

            return $this->formatProductDataForSearch($product, $searchTerm);
        } catch (ModelNotFoundException $e) {
            Log::warning("لم يتم العثور على المنتج بالكود: $searchTerm");
            return ['error' => 'المنتج غير موجود'];
        } catch (\Exception $e) {
            Log::error("خطأ غير متوقع أثناء البحث عن المنتج: " . $e->getMessage());
            return ['error' => 'حدث خطأ أثناء البحث عن المنتج'];
        }
    }


    private function formatProductDataForSearch(Product $product, string $searchTerm): array
    {
        $productVariantId = $product->is_variant ? $product->product_variant_id : null;
        $productCode = $product->is_variant ? $product->item_code : $product->code;

        $productInfo = explode("|", $searchTerm);

        return [
            $product->name,
            $productCode,
            $product->id,
            $productVariantId,
            $productInfo[1] ?? '',
        ];
    }
}
