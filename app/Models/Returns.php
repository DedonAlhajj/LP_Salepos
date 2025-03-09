<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Returns extends Model implements HasMedia
{
    use BelongsToTenant;
    use InteractsWithMedia;

	protected $table = 'returns';
    protected $fillable =[
        "reference_no", "user_id", "sale_id", "cash_register_id", "customer_id", "warehouse_id", "biller_id", "account_id", "currency_id", "exchange_rate", "item", "total_qty", "total_discount", "total_tax", "total_price","order_tax_rate", "order_tax", "grand_total", "document", "return_note", "staff_note"
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('returns')
            ->useDisk('returns')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

    }

    public function biller()
    {
    	return $this->belongsTo('App\Models\Biller');
    }


    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }



    public function productReturns()
    {
        return $this->hasMany(ProductReturn::class, 'return_id');
    }

    public function saleUnit()
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }

    public function scopeFilterByProduct($query, $product_id)
    {
        if ($product_id) {
            return $query->whereHas('productReturns', fn($q) => $q->where('product_id', $product_id));
        }
        return $query;
    }

    public function scopeFilterByWarehouse($query, $warehouse_id)
    {
        return $warehouse_id ? $query->where('warehouse_id', $warehouse_id) : $query;
    }

    public function scopeFilterByDateRange($query, $starting_date, $ending_date)
    {
        if ($starting_date && $ending_date) {
            return $query->whereBetween('returns.created_at', [$starting_date, $ending_date]);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'LIKE', "%{$search}%")
                    ->orWhereDate('created_at', date('Y-m-d', strtotime(str_replace('/', '-', $search))));
            });
        }
        return $query;
    }

    public function scopeForUserAccess($query)
    {
        $user = Auth::guard('web')->user();

        if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') === 'own') {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function scopeForUserAccessWarehouse(Builder $query,$warehouse_id)
    {
        $user = Auth::guard('web')->user();

        if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') == 'own') {
            return $query->where('user_id', Auth::id());
        } elseif (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') == 'warehouse') {
            return $query->where('warehouse_id', Auth::user()->warehouse_id);
        } elseif ($warehouse_id != 0) {
            return $query->where('warehouse_id', $warehouse_id);
        }
        return $query;
    }




}
