<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Tax extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[
        "name", "rate", "woocommerce_tax_id"
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'tax_id');
    }

    public function product()
    {
    	return $this->hasMany('App\ModelsProduct');
    }
}
