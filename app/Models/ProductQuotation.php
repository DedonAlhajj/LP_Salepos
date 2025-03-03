<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuotation extends Model
{
    protected $table = 'product_quotation';
    protected $fillable =[
        "quotation_id", "product_id", "product_batch_id", "variant_id", "qty", "sale_unit_id", "net_unit_price", "discount", "tax_rate", "tax", "total"
    ];

    // 🔹 العلاقة مع المنتج الرئيسي
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // 🔹 العلاقة مع المنتج المتغير (في حالة كان المنتج يحتوي على متغيرات مثل الحجم أو اللون)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // 🔹 العلاقة مع وحدة البيع (مثلاً: كجم، لتر، قطعة، إلخ)
    public function saleUnit()
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }

    // 🔹 العلاقة مع دفعة المنتج (إذا كان المنتج له باتشات أو أرقام دفعات)
    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    // العلاقة مع Quotation
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }


}
