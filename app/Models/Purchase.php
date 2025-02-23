<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{
    protected $fillable =[
        "reference_no", "user_id", "warehouse_id", "supplier_id", "currency_id", "exchange_rate", "item", "total_qty", "total_discount", "total_tax", "total_cost", "order_tax_rate", "order_tax", "order_discount", "shipping_cost", "grand_total","paid_amount", "status", "payment_status", "document", "note", "created_at"
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function products()
    {
        return $this->hasMany(ProductPurchase::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // في نموذج Purchase
    public function scopeForProduct($query, $product_id)
    {
        return $query->whereHas('products', function ($q) use ($product_id) {
            $q->where('product_id', $product_id);
        });
    }

    public function scopeForWarehouse($query, $warehouse_id)
    {
        if ($warehouse_id) {
            return $query->where('warehouse_id', $warehouse_id);
        }

        return $query;
    }

    public function scopeForDateRange($query, $starting_date, $ending_date)
    {
        return $query->whereBetween('created_at', [$starting_date, $ending_date]);
    }

    public function scopeForUserAccess($query)
    {
        $user = Auth::guard('web')->user();
        if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') == 'own') {
            return $query->where('user_id', Auth::id());
        }

        return $query;
    }


    public function scopeFilterByPurchaseStatus($query, $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeFilterByPaymentStatus($query, $status)
    {
        return $status ? $query->where('payment_status', $status) : $query;
    }
    public function scopeStaffAccessCheck($query)
    {
        $user = Auth::guard('web')->user();
        if(!$user->hasRole(['Admin','Owner']) && config('staff_access') == 'own')
            return $query->where('user_id', Auth::id());
        elseif(!$user->hasRole(['Admin','Owner']) && config('staff_access') == 'warehouse')
            return $query->where('warehouse_id', Auth::user()->warehouse_id);

        return $query;
    }

}
