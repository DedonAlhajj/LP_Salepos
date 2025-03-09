<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use App\Models\ProductPurchase;
use App\Models\ProductTransfer;
use App\Models\ProductVariant;
use App\Models\Transfer;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{

    public static function updateStock($productId, $warehouseId, $stock)
    {
        $productWarehouse = Product_Warehouse::firstOrNew([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
        ]);

        $productWarehouse->qty += $stock;
        $productWarehouse->save();
    }

    public function updateStockPurchase(Product $product, array $data, int $key, float $receivedValue, ProductPurchase $productPurchase): void
    {
        if ($product->is_variant) {
            $variant = ProductVariant::FindExactProductWithCode($product->id, $data['product_code'][$key])->firstOrFail();
            $warehouseProduct = Product_Warehouse::where([
                ['product_id', $product->id],
                ['variant_id', $variant->id],
                ['warehouse_id', $data['warehouse_id']]
            ])->first();

            $variant->increment('qty', $receivedValue);
            $productPurchase->variant_id = $variant->id;
        } else {
            $warehouseProduct = Product_Warehouse::where([
                ['product_id', $product->id],
                ['warehouse_id', $data['warehouse_id']]
            ])->first();
        }

        if (!empty($data['batch_no'][$key])) {
            $batch = ProductBatch::firstOrCreate([
                'product_id' => $product->id,
                'batch_no' => $data['batch_no'][$key],
            ], [
                'expired_date' => $data['expired_date'][$key] ?? null,
                'qty' => $receivedValue,
            ]);

            $productPurchase->product_batch_id = $batch->id;
            $batch->increment('qty', $receivedValue);
        }

        if ($warehouseProduct) {
            $warehouseProduct->increment('qty', $receivedValue);
        } else {
            Product_Warehouse::create([
                'product_id' => $product->id,
                'product_batch_id' => $productPurchase->product_batch_id ?? null,
                'variant_id' => $productPurchase->variant_id ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'qty' => $receivedValue,
            ]);
        }

        $product->increment('qty', $receivedValue);
    }

    public function updateStockTransfer(Transfer $transfer, ProductTransfer $productTransfer, int $status)
    {

        $fromWarehouse = Product_Warehouse::findProductWithoutVariant($productTransfer->product_id, $transfer->from_warehouse_id)->first();
        $toWarehouse = Product_Warehouse::findProductWithoutVariant($productTransfer->product_id, $transfer->to_warehouse_id)->first();


        if (!$fromWarehouse || !$toWarehouse) {
            throw new \Exception('Warehouses Is No Available For Transfer');
        }

        if ($status == 1) {
            $fromWarehouse->decrement('qty', $productTransfer->qty);
            $toWarehouse->increment('qty', $productTransfer->qty);
        } elseif ($status == 3) {
            $fromWarehouse->increment('qty', $productTransfer->qty);
        }

        $fromWarehouse->save();
        $toWarehouse->save();
    }

    public function updateInventory($product, Unit $unit, int $quantity, array $transferData)
    {
        // حساب الكمية بناءً على وحدة القياس
        $quantityAdjusted = $unit->operator == '*'
            ? $quantity * $unit->operation_value
            : $quantity / $unit->operation_value;

        // تقليل المخزون من المستودع الأصلي
        $productWarehouseFrom = Product_Warehouse::where('product_id', $product->id)
            ->where('warehouse_id', $transferData['from_warehouse_id'])
            ->first();

        if ($productWarehouseFrom) {
            $productWarehouseFrom->decrement('qty', $quantityAdjusted);
        }

        // إضافة المخزون إلى المستودع المستهدف
        if ($transferData['status'] == 1) {
            $productWarehouseTo = Product_Warehouse::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $transferData['to_warehouse_id'],
                ],
                [
                    'qty' => $quantityAdjusted
                ]
            );
            // إضافة الكمية إذا كانت موجودة
            if (!$productWarehouseTo->exists) {
                $productWarehouseTo->increment('qty', $quantityAdjusted);
            }
        }
    }

}
