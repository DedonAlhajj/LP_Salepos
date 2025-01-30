<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingSlip extends Model
{protected $fillable = ["reference_no", "sale_id", "delivery_id", "amount", "status"];

    public function sale()
    {
    	return $this->belongsTo('App\Models\Sale');
    }

    public function delivery()
    {
    	return $this->belongsTo('App\Models\Delivery');
    }

    public function products()
    {
    	return $this->belongsToMany('App\Models\Product', 'packing_slip_products');
    }
}
