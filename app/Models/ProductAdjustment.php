<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAdjustment extends Model
{
    protected $table = 'product_adjustments';
    protected $fillable =[
        "adjustment_id", "product_id", "variant_id", "unit_cost", "qty", "action"
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->select('id', 'name', 'code');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id')->select('id', 'item_code');
    }

    public function getProductNameAttribute()
    {
        return $this->variant ? "{$this->product->name} ({$this->variant->item_code})" : $this->product->name;
    }
}
