<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Unit extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[

        "unit_code", "unit_name", "base_unit", "operator", "operation_value", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }

    public function productSales()
    {
        return $this->hasMany(Product_Sale::class, 'sale_unit_id');
    }
}
