<?php

namespace App\Services\Tenant;

use App\Models\ProductReturn;

class ProductReturnService
{
    public function getProductReturnData($id)
    {
        $productReturnData = ProductReturn::with([
            'product:id,name,code',
            'productVariant:id,product_id,item_code',
            'productBatch:id,product_id,batch_no',
            'unit:id,unit_code'
        ])
            ->where('return_id', $id)
            ->get(['return_id', 'product_id', 'sale_unit_id', 'variant_id', 'product_batch_id', 'imei_number', 'qty', 'tax', 'tax_rate', 'discount', 'total']);

        return $productReturnData->map(function($item) {
            return [
                'product' => $item->product->name . ' [' . $item->product->code . ']',
                'imei_number' => $item->imei_number ? 'IMEI or Serial Number: ' . $item->imei_number : null,
                'quantity' => $item->qty,
                'unit' => $item->unit->unit_code,
                'tax' => $item->tax,
                'tax_rate' => $item->tax_rate,
                'discount' => $item->discount,
                'total' => $item->total,
                'batch_no' => $item->productBatch ? $item->productBatch->batch_no : 'N/A'
            ];
        });
    }

}
