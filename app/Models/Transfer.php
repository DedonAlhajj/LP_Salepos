<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transfer extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable =[

        "reference_no", "user_id", "status", "from_warehouse_id", "to_warehouse_id", "item", "total_qty", "total_tax", "total_cost", "shipping_cost", "grand_total", "document", "note", "is_sent", "created_at"
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('transfers')
            ->useDisk('transfers')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

    }
    public function productTransfers()
    {
        return $this->hasMany(ProductTransfer::class, 'transfer_id');
    }
    public function fromWarehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse', 'from_warehouse_id');
    }

    public function toWarehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse', 'to_warehouse_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function scopeFilterByUserAccess(Builder $query)
    {
        $user = Auth::guard('web')->user();
        if (!$user->hasRole(['Admin', 'Owner'])) {
            if (config('staff_access') === 'own') {
                return $query->where('user_id', Auth::id());
            }
            if (config('staff_access') === 'warehouse') {
                return $query->where(function ($q) {
                    $q->where('from_warehouse_id', Auth::user()->warehouse_id)
                        ->orWhere('to_warehouse_id', Auth::user()->warehouse_id);
                });
            }
        }
        return $query;
    }

    public function scopeFromWarehouse(Builder $query, $from_warehouse_id)
    {
        if ($from_warehouse_id) {
            return $query->where('from_warehouse_id', $from_warehouse_id);
        }
        return $query;
    }
    public function scopeToWarehouse(Builder $query, $to_warehouse_id)
    {
        if ($to_warehouse_id) {
            return $query->where('to_warehouse_id', $to_warehouse_id);
        }
        return $query;
    }

    public function scopeFilterByDateRange(Builder $query, $startingDate, $endingDate)
    {
        return $query->whereBetween('created_at', [$startingDate, $endingDate]);
    }
}
