<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingSlipProduct extends Model
{
    use HasFactory;
    protected $fillable = ["packing_slip_id", "product_id", "variant_id"];


    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function variant()
    {
        return $this->belongsTo('App\Models\Variant');
    }
}
