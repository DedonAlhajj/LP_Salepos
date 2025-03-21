<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Warehouse extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[
'tenant_id',
        "name", "phone", "email", "address"
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
            ->withPivot('qty');
    }

    public function product()
    {
    	return $this->hasMany('App\Models\Product');

    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
