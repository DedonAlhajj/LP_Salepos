<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product_Warehouse extends Model
{
	protected $table = 'product_warehouse';
    protected $fillable =[
        "product_id", "product_batch_id", "variant_id", "imei_number", "warehouse_id", "qty", "price"
    ];

    public function scopeFindProductWithVariant($query, $product_id, $variant_id, $warehouse_id)
    {
    	return $query->where([
            ['product_id', $product_id],
            ['variant_id', $variant_id],
            ['warehouse_id', $warehouse_id]
        ]);
    }

    public function scopeFindProductWithoutVariant($query, $product_id, $warehouse_id)
    {
    	return $query->where([
            ['product_id', $product_id],
            ['warehouse_id', $warehouse_id]
        ]);
    }

    public function scopeByProductAndWarehouse(Builder $query, $productId, $warehouseId)
    {
        return $query->where('product_id', $productId)->where('warehouse_id', $warehouseId);
    }

}
