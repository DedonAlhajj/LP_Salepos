<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quotation extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable =[

        "reference_no", "user_id", "biller_id", "supplier_id", "customer_id", "warehouse_id", "item", "total_qty", "total_discount", "total_tax", "total_price", "order_tax_rate", "order_tax", "order_discount", "shipping_cost", "grand_total", "quotation_status","document", "note"
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('Quotation')
            ->useDisk('Quotation')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

    }
    public function biller() { return $this->belongsTo(Biller::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function warehouse(){return $this->belongsTo(Warehouse::class);}
    // العلاقة مع ProductQuotation
    public function productQuotations()
    {
        return $this->hasMany(ProductQuotation::class, 'quotation_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quotation')
            ->withPivot([
                'variant_id',
                'product_batch_id',
                'qty',
                'net_unit_price',
                'discount',
                'tax',
                'tax_rate',
                'total'
            ])
            ->withTimestamps();
    }
    public function scopeFilterByUserAccess(Builder $query, $user)
    {
        if (!$user->hasRole(['Admin', 'Owner'])) {
            if (config('staff_access') === 'own') {
                return $query->where('user_id', $user->id);
            }
            if (config('staff_access') === 'warehouse') {
                return $query->where('warehouse_id', $user->warehouse_id);
            }
        }
        return $query;
    }

    public function scopeFilterByWarehouse(Builder $query, $warehouseId)
    {
        return $warehouseId != 0 ? $query->where('warehouse_id', $warehouseId) : $query;
    }

    public function scopeFilterByDateRange(Builder $query, $startingDate, $endingDate)
    {
        return $query->whereBetween('created_at', [$startingDate, $endingDate]);
    }

}
