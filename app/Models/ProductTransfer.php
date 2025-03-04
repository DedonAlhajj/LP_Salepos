<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    protected $table = 'product_transfer';
    protected $fillable =[

        "transfer_id", "product_id", "product_batch_id", "variant_id", "imei_number", "qty", "purchase_unit_id", "net_unit_cost", "tax_rate", "tax", "total"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseUnit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }


    // علاقة مع الوحدة (Unit)
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    // علاقة مع المنتج المتغير (ProductVariant)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // علاقة مع الدفعة (ProductBatch)
    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }
}
