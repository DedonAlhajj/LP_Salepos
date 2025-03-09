<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $table = 'product_returns';
    protected $fillable =[
        "return_id", "product_id", "variant_id", "imei_number", "product_batch_id", "qty", "sale_unit_id", "net_unit_price", "discount", "tax_rate", "tax", "total"
    ];

    public function returns()
    {
        return $this->belongsTo(Returns::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // العلاقة مع متغير المنتج (ProductVariant)
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // العلاقة مع دفعة المنتج (ProductBatch)
    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    // العلاقة مع الوحدة (Unit)
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax');
    }

}
