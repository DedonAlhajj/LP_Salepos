<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Sale extends Model
{
    use BelongsToTenant;

    protected $fillable =[
        "tenant_id",
        "reference_no", "user_id", "cash_register_id", "table_id", "queue",
        "customer_id", "warehouse_id", "biller_id", "item", "total_qty",
        "total_discount", "total_tax", "total_price", "order_tax_rate",
        "order_tax", "order_discount_type", "order_discount_value",
        "order_discount", "coupon_id", "coupon_discount", "shipping_cost",
        "grand_total", "currency_id", "exchange_rate", "sale_status",
        "payment_status", "billing_name", "billing_phone", "billing_email",
        "billing_address", "billing_city", "billing_state", "billing_country",
        "billing_zip", "shipping_name", "shipping_phone", "shipping_email",
        "shipping_address", "shipping_city", "shipping_state","shipping_country",
        "shipping_zip", "sale_type", "paid_amount", "document", "sale_note", "staff_note",
        "created_at", "woocommerce_order_id"
    ];

  /*  public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_sales');
    }
*/
    public function biller()
    {
        return $this->belongsTo('App\Models\Biller');
    }

    public function table()
    {
        return $this->belongsTo('App\Models\Table');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function products()
    {
        return $this->hasMany(Product_Sale::class);
    }

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
            $query->where('sales.user_id', Auth::id());
        }

        return $query;
    }

    public function scopeWhereCompletedReference(Builder $query, string $referenceNo)
    {
        return $query->where([
            ['reference_no', $referenceNo],
            ['sale_status', 1]
        ]);
    }
}
