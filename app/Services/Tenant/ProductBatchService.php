<?php

namespace App\Services\Tenant;


use App\Models\ProductBatch;
use Illuminate\Support\Collection;

class ProductBatchService
{

    public function getBatchNo($productPurchaseData)
    {
        if ($productPurchaseData->product_batch_id) {
            return ProductBatch::findOrFail($productPurchaseData->product_batch_id)->batch_no;
        }
        return 'N/A';
    }

    public function getProductBatches(array $batchIds): Collection
    {
        return ProductBatch::whereIn('id', $batchIds)
            ->select('id', 'batch_no', 'expired_date')
            ->get()
            ->keyBy('id');
    }

    public function updateProductBatchQuantity($batchId, $warehouseId, $quantity, $operation = 'increment')
    {
        $productBatch = ProductBatch::where([
            'id' => $batchId,
            'warehouse_id' => $warehouseId
        ])->first();

        if ($productBatch) {
            $productBatch->{$operation}('qty', $quantity);
        }
    }

}

