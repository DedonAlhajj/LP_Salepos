<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProductReturn extends Model
{
    protected $table = 'purchase_product_return';
    protected $fillable =[
        "return_id", "product_id", "product_batch_id", "variant_id", "imei_number", "qty", "purchase_unit_id", "net_unit_cost", "discount", "tax_rate", "tax", "total"
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

}
