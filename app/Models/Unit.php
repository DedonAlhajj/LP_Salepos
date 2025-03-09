<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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


    public static function getUnitsForProduct(int $unitId, int $saleUnitId)
    {
        return self::where("base_unit", $unitId)
            ->orWhere('id', $unitId)
            ->orderByRaw("id = ? DESC", [$saleUnitId])
            ->get(['unit_name', 'operator', 'operation_value']);
    }

    public function scopeBaseOrSelf(Builder $query, $unitId)
    {
        return $query->where('base_unit', $unitId)->orWhere('id', $unitId);
    }

}
