<?php

namespace App\Services\Tenant;


use App\Models\ProductBatch;

class ProductBatchService
{

    public function getBatchNo($productPurchaseData)
    {
        if ($productPurchaseData->product_batch_id) {
            return ProductBatch::findOrFail($productPurchaseData->product_batch_id)->batch_no;
        }
        return 'N/A';
    }

}

