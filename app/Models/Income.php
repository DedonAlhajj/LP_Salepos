<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Income extends Model
{
    use HasFactory;

    protected $fillable =[
        "reference_no", "income_category_id", "warehouse_id", "account_id", "user_id", "cash_register_id", "amount", "note", "created_at"
    ];

    public function warehouse()
    {
    	return $this->belongsTo(Warehouse::class);
    }

    public function incomeCategory() {
    	return $this->belongsTo(IncomeCategory::class);
    }

    public function scopeFilterByDate(Builder $query, $starting_date, $ending_date)
    {
        return $query->whereBetween('created_at', [$starting_date, $ending_date]);
    }

    public function scopeApplyStaffAccess(Builder $query)
    {
        $user = Auth::guard('web')->user();

        if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') === 'own') {
            return $query->where('user_id',$user->id);
        } elseif (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') === 'warehouse') {
            return $query->where('warehouse_id', $user->warehouse_id);
        }
        return $query;
    }

    public function scopeApplyWarehouseFilter(Builder $query, $warehouse_id)
    {
        if ($warehouse_id) {
            return $query->where('warehouse_id', $warehouse_id);
        }
        return $query;
    }
}
