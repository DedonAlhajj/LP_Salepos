<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product_Sale extends Model
{
	protected $table = 'product_sales';
    protected $fillable =[
        "sale_id", "product_id", "product_batch_id", "variant_id", 'imei_number', "qty", "return_qty", "sale_unit_id", "net_unit_price", "discount", "tax_rate", "tax", "total", "is_packing", "is_delivered"
    ];


    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_rate', 'rate');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }
}
