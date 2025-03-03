<?php

namespace App\Services\Tenant;



// ProductPurchaseService.php
use App\Models\ProductPurchase;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;

class ProductPurchaseService
{
    protected $productService;
    protected $unitService;
    protected $productVariantService;
    protected $productBatchService;
    protected StockService $inventoryService;

    public function __construct(
        ProductService          $productService,
        UnitService             $unitService,
        ProductVariantService   $productVariantService,
        ProductWarehouseService $productBatchService,
        StockService            $inventoryService,
    ) {
        $this->productService = $productService;
        $this->unitService = $unitService;
        $this->productVariantService = $productVariantService;
        $this->productBatchService = $productBatchService;
        $this->inventoryService = $inventoryService;
    }


  /*  public function __construct(
        private UnitService $unitService,
        private ProductService $productService,
        private InventoryService $inventoryService
    ) {}*/

    public function updateProductPurchases(Purchase $purchase, array $data): void
    {
        foreach ($data['product_id'] as $key => $productId) {
            // 1. حساب الكمية المستلمة بناءً على الوحدة
            $receivedValue = $this->unitService->calculateReceivedValue(
                $data['purchase_unit'][$key],
                $data['recieved'][$key]
            );

            // 2. جلب المنتج
            $product = $this->productService->getProductById($productId);

            // 3. إنشاء عملية شراء المنتج
            $productPurchase = new ProductPurchase([
                'purchase_id' => $purchase->id,
                'product_id' => $productId,
                'qty' => $data['qty'][$key],
                'recieved' => $data['recieved'][$key],
                'purchase_unit_id' => $this->unitService->getUnitId($data['purchase_unit'][$key]),
                'net_unit_cost' => $data['net_unit_cost'][$key],
                'discount' => $data['discount'][$key],
                'tax_rate' => $data['tax_rate'][$key],
                'tax' => $data['tax'][$key],
                'total' => $data['subtotal'][$key],
                'imei_number' => $data['imei_number'][$key] ?? null,
            ]);

            // 4. تحديث المخزون عبر `InventoryService`
            $this->inventoryService->updateStockPurchase(
                $product,
                $data,
                $key,
                $receivedValue,
                $productPurchase
            );

            // 5. حفظ بيانات الشراء
            $productPurchase->save();
        }
    }
    public function getProductPurchaseData($purchaseId)
    {
        try {
            $productPurchases = ProductPurchase::with([
                'product',
                'unit',
                'productVariant',
                'productBatch'
            ])->where('purchase_id', $purchaseId)->get();

            return $productPurchases->map(function ($productPurchaseData) {
                // Get product data via product service
                $product = $this->productService->getProductData($productPurchaseData->product_id);
                // Get unit data via unit service
                $unit = $this->unitService->getUnit($productPurchaseData->purchase_unit_id);

                $productPurchase = [];
                $productPurchase[0] = $product->name . ' [' . $product->code . ']';

                // If there is an IMEI number
                if ($productPurchaseData->imei_number) {
                    $productPurchase[0] .= '<br>IMEI or Serial Number: ' . $productPurchaseData->imei_number;
                }

                $productPurchase[1] = $productPurchaseData->qty;
                $productPurchase[2] = $unit->unit_code;
                $productPurchase[3] = $productPurchaseData->tax;
                $productPurchase[4] = $productPurchaseData->tax_rate;
                $productPurchase[5] = $productPurchaseData->discount;
                $productPurchase[6] = $productPurchaseData->total;

                // Fetch batch data via batch service
                $productPurchase[7] = $this->productBatchService->getBatchNo($productPurchaseData);

                return $productPurchase;
            });
        } catch (\Exception $e) {
            // Improved error handling
            Log::error("Error Purchase fetching modifications11: " . $e->getMessage());
            throw new \Exception('Something went wrong while fetching product purchase data: ' . $e->getMessage());
        }
    }


}

