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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // المتغير المرتبط بـ Product_Warehouse
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // الدفعة المرتبطة بـ Product_Warehouse
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function scopeFindProductWithVariant($query, $product_id, $variant_id, $warehouse_id)
    {
    	return $query->where([
            ['product_id', $product_id],
            ['variant_id', $variant_id],
            ['warehouse_id', $warehouse_id]
        ]);
    }

    public function scopeFindProductWithoutVariant(Builder $query, $product_id, $warehouse_id)
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

    public function scopeInWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('warehouse_id', $warehouseId)->where('qty', '>', 0);
    }


}
