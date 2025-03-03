<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use App\Models\ProductPurchase;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Tax;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use function PHPUnit\Framework\throwException;

class PurchaseService
{

    private $supplierService;
    private $warehouseService;
    private $taxService;
    private $currencyService;
    private $productService;
    protected ProductWarehouseService $productBatchService;
    protected ProductVariantService $productVariantService;
    protected ProductPurchaseService $productPurchaseService;
    protected UnitService $unitService;

    public function __construct(
        SupplierService         $supplierService,
        WarehouseService        $warehouseService,
        TaxCalculatorService    $taxService,
        CurrencyService         $currencyService,
        ProductService          $productService,
        ProductWarehouseService $productBatchService,
        ProductVariantService   $productVariantService,
        UnitService             $unitService,
        ProductPurchaseService  $productPurchaseService
    ) {
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->taxService = $taxService;
        $this->currencyService = $currencyService;
        $this->productService = $productService;
        $this->productBatchService = $productBatchService;
        $this->productVariantService = $productVariantService;
        $this->unitService = $unitService;
        $this->productPurchaseService = $productPurchaseService;
    }


    public static function autoPurchase($product, $warehouseId, $stock)
    {
        // حساب الضرائب والتكلفة
        $costData = TaxCalculatorService::calculate($product, $stock);

        // بيانات الشراء
        $purchaseData = [
            'reference_no' => 'pr-' . date("Ymd") . '-' . date("his"),
            'user_id' => Auth::id(),
            'warehouse_id' => $warehouseId,
            'item' => 1,
            'total_qty' => $stock,
            'total_discount' => 0,
            'status' => 1,
            'payment_status' => 2,
            'total_tax' => $costData['tax'],
            'total_cost' => $costData['total_cost'],
            'order_tax' => 0,
            'grand_total' => $costData['total_cost'],
            'paid_amount' => $costData['total_cost'],
        ];

        // إنشاء سجل الشراء
        $purchase = Purchase::create($purchaseData);

        // إنشاء سجل تفاصيل الشراء
        ProductPurchase::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'qty' => $stock,
            'recieved' => $stock,
            'purchase_unit_id' => $product->unit_id,
            'net_unit_cost' => $costData['net_unit_cost'],
            'discount' => 0,
            'tax_rate' => $costData['tax_rate'],
            'tax' => $costData['tax'],
            'total' => $costData['total_cost'],
        ]);

        // تحديث المخزون
        StockService::updateStock($product->id, $warehouseId, $stock);

        // إنشاء سجل الدفع
        Payment::create([
            'payment_reference' => 'ppr-' . date("Ymd") . '-' . date("his"),
            'user_id' => Auth::id(),
            'purchase_id' => $purchase->id,
            'account_id' => 0,
            'amount' => $costData['total_cost'],
            'change' => 0,
            'paying_method' => 'Cash',
        ]);
    }


    /** Show Purchases Data */
    public function getPurchases($filters)
    {
        try {
            $query = Purchase::with(['supplier', 'warehouse'])
                ->forDateRange($filters['starting_date'], $filters['ending_date'])
                ->forWarehouse($filters['warehouse_id'])
                ->filterByPurchaseStatus($filters['purchase_status'])
                ->filterByPaymentStatus($filters['payment_status'])
                ->staffAccessCheck();

            $purchases = $query->orderBy('created_at', 'desc')->get();

            return $this->formatPurchases($purchases);

        } catch (\Exception $e) {
            Log::error("Error Purchase fetching modifications: " . $e->getMessage());
            throw new Exception("Purchase operation failed : " . $e->getMessage());
        }
    }

    private function formatPurchases($purchases)
    {
        return $purchases->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'date' => date(config('date_format'), strtotime($purchase->created_at->toDateString())),
                'reference_no' => $purchase->reference_no,
                'supplier' => [
                    'name' => $purchase->supplier->name ?? 'N/A',
                    'company_name' => $purchase->supplier->company_name ?? 'N/A',
                    'email' => $purchase->supplier->email ?? 'N/A',
                    'phone' => $purchase->supplier->phone_number ?? 'N/A',
                    'address' => preg_replace('/\s+/S', " ", $purchase->supplier->address ?? 'N/A'),
                    'city' => $purchase->supplier->city ?? 'N/A',
                ],
                'warehouse' => [
                    'name' => $purchase->warehouse->name ?? 'N/A',
                    'phone' => $purchase->warehouse->phone ?? 'N/A',
                    'address' => preg_replace('/\s+/S', " ", $purchase->warehouse->address ?? 'N/A'),
                ],
                'purchase_status' => $this->formatPurchaseStatus($purchase->status),
                'payment_status' => $this->formatPaymentStatus($purchase->payment_status),
                'total_tax' => number_format($purchase->total_tax, config('decimal')),
                'total_discount' => number_format($purchase->total_discount, config('decimal')),
                'total_cost' => number_format($purchase->total_cost, config('decimal')),
                'order_tax' => number_format($purchase->order_tax, config('decimal')),
                'order_tax_rate' => number_format($purchase->order_tax_rate, config('decimal')),
                'order_discount' => number_format($purchase->order_discount, config('decimal')),
                'shipping_cost' => number_format($purchase->shipping_cost, config('decimal')),
                'grand_total' => number_format($purchase->grand_total, config('decimal')),
                'returned_amount' => number_format(DB::table('return_purchases')->where('purchase_id', $purchase->id)->sum('grand_total'), config('decimal')),
                'paid_amount' => number_format($purchase->paid_amount, config('decimal')),
                'due' => number_format($purchase->grand_total - $purchase->paid_amount, config('decimal')),
                'note' => preg_replace('/\s+/S', " ", $purchase->note ?? 'N/A'),
                'created_by' => [
                    'name' => $purchase->user->name ?? 'N/A',
                    'email' => $purchase->user->email ?? 'N/A',
                ],
                'document' => $purchase->document ?? null,
                'currency' => [
                    'code' => $purchase->currency->code ?? 'N/A',
                    'exchange_rate' => $purchase->exchange_rate ?? 'N/A',
                ]
            ];
        });

    }

    private function formatPurchaseStatus($status)
    {
        return match ($status) {
            1 => trans('file.Recieved'),
            2 => trans('file.Partial'),
            3 => trans('file.Pending'),
            default => trans('file.Ordered'),
        };
    }

    private function formatPaymentStatus($status)
    {
        return $status == 1 ? trans('file.Due') : trans('file.Paid');
    }

    public function getFilters($request)
    {
        return [
            'warehouse_id' => $request->input('warehouse_id', 0),
            'purchase_status' => $request->input('purchase_status', 0),
            'payment_status' => $request->input('payment_status', 0),
            'starting_date' => $request->input('starting_date', now()->subYear()->format('Y-m-d')),
            'ending_date' => $request->input('ending_date', now()->format('Y-m-d')),
        ];
    }


    /** Create */
    public function getAllPurchaseData($user)
    {
        return [
            'suppliers' => $this->supplierService->getSuppliers(),
            'warehouses' => $this->warehouseService->getWarehousesById($user),
            'taxes' => $this->taxService->getTaxes(),
            'currencies' => $this->currencyService->getCurrencies(),
            'products_without_variant' => $this->productService->getProductsWithoutVariant(),
            'products_with_variant' => $this->productService->getProductsWithVariant(),
        ];
    }


    /** Store functions  */
    public function storePurchase(array $data): Purchase
    {
        DB::beginTransaction();

        try {
            // إعداد بيانات الشراء
            $purchaseData = $this->preparePurchaseData($data);

            // إنشاء سجل الشراء
            $purchase = new Purchase($purchaseData);

            // التحقق وتحميل المستند باستخدام Media Library
            if (!empty($data['document'])) {
                $this->validateDocument($data['document']);
                $purchase->save();
                $purchase->addMedia($data['document'])->toMediaCollection('purchase_documents');
            } else {
                $purchase->save();
            }

            // معالجة المنتجات المرتبطة بالشراء
            $this->processPurchaseProducts($purchase, $data);

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollBack();
           // Log::error("Error Purchase fetching modifications: " . $e->getMessage());
            throw new Exception("Purchase operation failed : " . $e->getMessage());
        }
    }

    private function preparePurchaseData(array $data): array
    {
        // استخراج الحقول التي يجب حفظها بناءً على `fillable` من الموديل
        $purchaseFields = (new Purchase())->getFillable();

        // فلترة البيانات بحيث لا يتم تمرير سوى الحقول المسموح بها
        $filteredData = array_intersect_key($data, array_flip($purchaseFields));

        // إضافة القيم الافتراضية
        return array_merge($filteredData, [
            'user_id' => Auth::id(),
            'reference_no' => 'pr-' . now()->format('Ymd-His'),
            'created_at' => $data['created_at'] ?? now(),
            'updated_at' => now(),
        ]);
    }

    private function validateDocument($document): void
    {
        $validator = Validator::make(
            ['extension' => strtolower($document->getClientOriginalExtension())],
            ['extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt']
        );

        if ($validator->fails()) {
            Log::error("Error document: ");
            throw new Exception("Purchase operation failed document : ");
        }
    }

    private function processPurchaseProducts(Purchase $purchase, array $data): void
    {
        $products = [];

        foreach ($data['product_id'] as $i => $productId) {
            $unit = Unit::where('unit_name', $data['purchase_unit'][$i])->firstOrFail();
            $product = Product::findOrFail($productId);

            $quantity = $this->calculateQuantity($unit, $data['recieved'][$i]);

            // تحديث كمية المنتج في المستودع
            $this->updateWarehouseProduct($productId, $data['warehouse_id'], $quantity);

            // تجهيز بيانات المنتجات المرتبطة بالشراء
            $products[] = $this->prepareProductPurchaseData($purchase->id, $data, $i, $unit->id);
        }

        ProductPurchase::insert($products);
    }

    private function calculateQuantity(Unit $unit, float $receivedQuantity): float
    {
        return ($unit->operator === '*') ? $receivedQuantity * $unit->operation_value : $receivedQuantity / $unit->operation_value;
    }

    private function updateWarehouseProduct(int $productId, int $warehouseId, float $quantity): void
    {
        $warehouseProduct = Product_Warehouse::firstOrNew([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
        ]);

        $warehouseProduct->qty += $quantity;
        $warehouseProduct->save();
    }

    private function prepareProductPurchaseData(int $purchaseId, array $data, int $index, int $unitId): array
    {
        return [
            'purchase_id' => $purchaseId,
            'product_id' => $data['product_id'][$index],
            'qty' => $data['qty'][$index],
            'recieved' => $data['recieved'][$index],
            'purchase_unit_id' => $unitId,
            'net_unit_cost' => $data['net_unit_cost'][$index],
            'discount' => $data['discount'][$index],
            'tax_rate' => $data['tax_rate'][$index],
            'tax' => $data['tax'][$index],
            'total' => $data['subtotal'][$index],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }


    /** Import Purchase Form file */
    public function purchaseByCsv()
    {
        return [
            'lims_supplier_list' => $this->supplierService->getSuppliers(),
            'lims_warehouse_list' => $this->warehouseService-> getWarehouses(),
            'lims_tax_list' => $this->taxService->getTaxes(),
        ];
    }

    /** Edit functions */
    public function getAllPurchaseDataEdit($purchase): array
    {
        try {
            $product_purchases = ProductPurchase::where('purchase_id', $purchase->id)->get();
            $currency_exchange_rate = $purchase->exchange_rate ?? 1;

            // جمع كل المعرفات لتجنب الاستعلامات المتكررة
            $productIds = $product_purchases->pluck('product_id')->toArray();
            $variantIds = $product_purchases->pluck('variant_id')->filter()->toArray();
            $taxRates = $product_purchases->pluck('tax_rate')->unique()->toArray();
            $batchIds = $product_purchases->pluck('product_batch_id')->filter()->toArray();

            // جلب البيانات دفعة واحدة
            $products = $this->productService->getProductsWhereIn($productIds);
            $productVariants = $this->productVariantService->getProductVariants($variantIds);
            $taxes = $this->taxService->getTaxesWhereIn($taxRates);
            $productBatches = $this->productBatchService->getProductBatches($batchIds);

            // تجهيز بيانات المنتجات المشتراة
            $product_purchase_data = $this->preparePurchaseProducts(
                $product_purchases,
                $products,
                $productVariants,
                $taxes,
                $productBatches
            );

            return [
                'suppliers' => $this->supplierService->getSuppliers(),
                'warehouses' => $this->warehouseService->getWarehouses(),
                'taxes' => $this->taxService->getTaxes(),
                'Purchases' => $purchase,
                'product_purchases' => $product_purchase_data,
                'currency' => $currency_exchange_rate,
                'products_without_variant' => $this->productService->getProductsWithoutVariant(),
                'products_with_variant' => $this->productService->getProductsWithVariant(),
            ];
        } catch (\Exception $e) {
            Log::error("Error fetching purchase data: " . $e->getMessage());
            return ['error' => 'حدث خطأ أثناء جلب بيانات الفاتورة.'];
        }
    }

    private function preparePurchaseProducts(
        Collection $product_purchases,
        Collection $products,
        Collection $productVariants,
        Collection $taxes,
        Collection $productBatches
    ): Collection {
        return $product_purchases->map(function ($product_purchase) use ($products, $productVariants, $taxes, $productBatches) {
            $product_data = $products[$product_purchase->product_id] ?? null;
            if (!$product_data) {
                Log::warning("Product ID {$product_purchase->product_id} not found.");
                return null;
            }

            // تعيين كود المنتج إذا كان هناك متغير
            $product_code = $product_data->code;
            if ($product_purchase->variant_id) {
                $variant = $productVariants[$product_purchase->variant_id] ?? null;
                if ($variant) {
                    $product_code = $variant->item_code;
                }
            }

            // استرجاع الضريبة
            $tax = $taxes[$product_purchase->tax_rate] ?? null;

            // استرجاع بيانات الوحدات
            $unit_data = $this->unitService->getUnitData($product_data->unit_id, $product_purchase->purchase_unit_id);

            // حساب تكلفة المنتج
            $product_cost = $this->productService->calculateProductCost(
                $product_purchase->net_unit_cost,
                $product_purchase->discount,
                $product_purchase->qty,
                $product_purchase->total,
                $unit_data['unit_operation_value'][0] ?? 1,
                $product_data->tax_method
            );

            // استرجاع بيانات الباتش
            $product_batch_data = $productBatches[$product_purchase->product_batch_id] ?? null;

            return [
                'product_purchase' => $product_purchase,
                'product_data' => $product_data,
                'tax' => $tax,
                'unit_name' => implode(",", $unit_data['unit_name']) . ',',
                'unit_operator' => implode(",", $unit_data['unit_operator']) . ',',
                'unit_operation_value' => implode(",", $unit_data['unit_operation_value']) . ',',
                'product_cost' => $product_cost,
                'product_batch_data' => $product_batch_data,
            ];
        })->filter()->values();
    }

    /** Update functions */
    public function updatePurchase(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);

            // ✅ تحديث حالة الدفع
            $balance = $data['grand_total'] - $data['paid_amount'];
            $data['payment_status'] = ($balance == 0) ? 2 : 1;

            // ✅ تحديث تاريخ الإنشاء
            $data['created_at'] = date("Y-m-d", strtotime(str_replace("/", "-", $data['created_at'])));

            // ✅ تحديث المستند (إن وجد) باستخدام Spatie Media Library
            if (isset($data['document'])) {
                $this->updateDocument($purchase, $data['document']);
                unset($data['document']);
            }

            // ✅ استرجاع المنتجات القديمة وإعادة المخزون إلى وضعه السابق
            $this->rollbackStock($purchase);

            // ✅ تحديث المنتجات وإعادة إدخالها
            $this->productPurchaseService->updateProductPurchases($purchase, $data);

            // ✅ تحديث بيانات الشراء
            $purchase->update($data);

            DB::commit();
            return ['success' => true, 'message' => 'Purchase updated successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error fetching purchase data update: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function updateDocument(Purchase $purchase, $document)
    {
        // حذف المستند القديم
        if ($purchase->hasMedia('documents')) {
            $purchase->clearMediaCollection('documents');
        }

        // حفظ المستند الجديد
        $purchase->addMedia($document)
            ->toMediaCollection('documents');
    }

    private function rollbackStock(Purchase $purchase)
    {
        $productPurchases = ProductPurchase::where('purchase_id', $purchase->id)->get();

        foreach ($productPurchases as $productPurchase) {
            $product = Product::find($productPurchase->product_id);
            $unit = Unit::find($productPurchase->purchase_unit_id);

            $oldReceivedValue = ($unit->operator == '*')
                ? $productPurchase->recieved * $unit->operation_value
                : $productPurchase->recieved / $unit->operation_value;

            if ($product->is_variant) {
                $productVariant = ProductVariant::FindExactProduct($product->id, $productPurchase->variant_id)->first();
                $productVariant->decrement('qty', $oldReceivedValue);
            } elseif ($productPurchase->product_batch_id) {
                $batch = ProductBatch::find($productPurchase->product_batch_id);
                $batch->decrement('qty', $oldReceivedValue);
            }

            $warehouseProduct = Product_Warehouse::where([
                ['product_id', $productPurchase->product_id],
                ['warehouse_id', $purchase->warehouse_id],
            ])->first();

            if ($warehouseProduct) {
                $warehouseProduct->decrement('qty', $oldReceivedValue);
            }

            $product->decrement('qty', $oldReceivedValue);
            $productPurchase->delete();
        }
    }

    /** Add an information for Purchase already exist */
    public function duplicate($id){
        try {
        $purchase = Purchase::findOrFail($id);
        $data = $this->getAllPurchaseDataEdit($purchase);
        return $data;
        } catch (\Exception $e) {
            Log::error("Error duplicate: " . $e->getMessage());
            return ['error' => 'error happen.'];
        }
    }


    /** Update Purchase Payment Status */
    public function updatePurchasePaymentStatus(Purchase $purchase, float $amount)
    {
        $purchase->paid_amount += $amount;
        $balance = $purchase->grand_total - $purchase->paid_amount;
        $purchase->payment_status = $balance === 0 ? 2 : 1;
        $purchase->save();
    }

    /** get Purchase Data*/
    public function getPurchase(int $purchaseId): Purchase
    {
        return Purchase::findOrFail($purchaseId);
    }


    /** Delete functions */
    public function deletePurchase(int $purchaseId)
    {
        return DB::transaction(function () use ($purchaseId) {
            $purchase = Purchase::findOrFail($purchaseId);
            $purchaseService = app(PaymentService::class);
            // حذف المنتجات المرتبطة بالمشتريات واسترجاع الكميات
            $this->rollbackPurchasedProducts($purchase);

            // حذف المدفوعات المرتبطة بالمشتريات
            $purchaseService->deleteRelatedPayments($purchase);

            // حذف المستندات الخاصة بالمشتريات
            if ($purchase->document) {
                $this->deletePurchaseDocument($purchase->document);
            }

            // حذف سجل الشراء
            $purchase->delete();
        });
    }

    private function deletePurchaseDocument(string $documentPath)
    {

    }

    private function rollbackPurchasedProducts(Purchase $purchase)
    {
        $productPurchases = ProductPurchase::where('purchase_id', $purchase->id)->get();

        foreach ($productPurchases as $productPurchase) {
            $product = Product::find($productPurchase->product_id);
            $unit = Unit::find($productPurchase->purchase_unit_id);

            // حساب الكمية المستلمة بوحدة القياس الصحيحة
            $receivedQty = ($unit->operator == '*')
                ? $productPurchase->recieved * $unit->operation_value
                : $productPurchase->recieved / $unit->operation_value;

            // تحديث المخزون بناءً على نوع المنتج (عادي، متغير، دفعات)
            if ($productPurchase->variant_id) {
                $productVariant = ProductVariant::FindExactProduct($product->id, $productPurchase->variant_id)->first();
                $productWarehouse = Product_Warehouse::FindProductWithVariant($product->id, $productPurchase->variant_id, $purchase->warehouse_id)->first();

                $productVariant->qty -= $receivedQty;
                $productVariant->save();
            } elseif ($productPurchase->product_batch_id) {
                $productBatch = ProductBatch::find($productPurchase->product_batch_id);
                $productWarehouse = Product_Warehouse::where([
                    ['product_batch_id', $productPurchase->product_batch_id],
                    ['warehouse_id', $purchase->warehouse_id]
                ])->first();

                $productBatch->qty -= $receivedQty;
                $productBatch->save();
            } else {
                $productWarehouse = Product_Warehouse::FindProductWithoutVariant($product->id, $purchase->warehouse_id)->first();
            }

            // تحديث المخزون
            $product->qty -= $receivedQty;
            $productWarehouse->qty -= $receivedQty;

            // تحديث IMEI إذا كان المنتج يحتوي عليه
            if ($productPurchase->imei_number) {
                $this->rollbackIMEINumbers($productWarehouse, $productPurchase->imei_number);
            }

            $productWarehouse->save();
            $product->save();

            // حذف العلاقة بين الشراء والمنتج
            $productPurchase->delete();
        }
    }

    private function rollbackIMEINumbers(Product_Warehouse $productWarehouse, string $imeiNumbers)
    {
        $imeiList = explode(',', $imeiNumbers);
        $currentIMEIs = explode(',', $productWarehouse->imei_number);

        foreach ($imeiList as $imei) {
            if (($index = array_search($imei, $currentIMEIs)) !== false) {
                unset($currentIMEIs[$index]);
            }
        }

        $productWarehouse->imei_number = implode(',', $currentIMEIs);
    }


}
