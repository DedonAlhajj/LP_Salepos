<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReturnPurchase extends Model
{
    protected $table = 'return_purchases';
    protected $fillable =[
        "reference_no", "purchase_id", "user_id", "supplier_id", "warehouse_id", "account_id", "currency_id", "exchange_rate", "item", "total_qty", "total_discount", "total_tax", "total_cost","order_tax_rate", "order_tax", "grand_total", "document", "return_note", "staff_note"
    ];


    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function productReturns()
    {
        return $this->hasMany(PurchaseProductReturn::class, 'return_id');
    }

    // العلاقة مع المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // العلاقة مع المستودع
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Scope لتصفية البيانات حسب المنتج
    public function scopeFilterByProduct($query, $product_id)
    {
        if ($product_id) {
            return $query->whereHas('productReturns', fn($q) => $q->where('product_id', $product_id));
        }
        return $query;
    }

    // Scope لتصفية البيانات حسب المستودع
    public function scopeFilterByWarehouse($query, $warehouse_id)
    {
        return $warehouse_id ? $query->where('warehouse_id', $warehouse_id) : $query;
    }

    // Scope لتصفية البيانات حسب تاريخ معين
    public function scopeFilterByDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    // Scope للحد من البيانات حسب صلاحيات المستخدم
    public function scopeForUserAccess($query)
    {
        $user = Auth::guard('web')->user();

        if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') == 'own') {
            return $query->where('user_id', auth()->id());
        }
        return $query;
    }

    // Scope للبحث حسب المرجع
    public function scopeSearch($query, $search)
    {
        return $search
            ? $query->where('reference_no', 'LIKE', "%{$search}%")
            : $query;
    }
}
