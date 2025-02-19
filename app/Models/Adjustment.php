<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Adjustment extends Model implements HasMedia
{
    use BelongsToTenant;
    use InteractsWithMedia;
    protected $fillable =[
        "reference_no", "warehouse_id", "document", "total_qty", "item",
         "note"
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('adjustment_doc')
            ->useDisk('adjustment_doc')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productAdjustments()
    {
        return $this->hasMany(ProductAdjustment::class);
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['warehouse:id,name', 'productAdjustments.product']);
    }
}
