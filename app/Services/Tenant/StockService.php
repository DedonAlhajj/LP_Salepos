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
use App\Models\ProductVariant;
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
}
