<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPurchase extends Model
{
    protected $table = 'product_purchases';
    protected $fillable =[

        "purchase_id", "product_id", "product_batch_id", "variant_id", "imei_number", "qty", "recieved", "purchase_unit_id", "net_unit_cost", "discount", "tax_rate", "tax", "total"
    ];



    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * العلاقة بين ProductPurchase و Unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    /**
     * العلاقة بين ProductPurchase و ProductVariant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * العلاقة بين ProductPurchase و ProductBatch
     */
    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }
}
