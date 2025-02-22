<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class StockCount extends Model implements HasMedia
{
    use BelongsToTenant;
    use InteractsWithMedia;

protected $fillable =[
        "reference_no", "warehouse_id", "brand_id", "category_id", "user_id", "type", "initial_file", "final_file", "note", "is_adjusted"
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stock_count_csv')
            ->useDisk('stock_count_csv')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط
    }
}
