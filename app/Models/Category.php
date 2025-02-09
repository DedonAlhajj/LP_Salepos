<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable =[

        "name", "parent_id", "is_sync_disable", "woocommerce_category_id"
        ,'slug', 'page_title', 'short_description'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->useDisk('category_images')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

        $this->addMediaCollection('category_icons')
            ->useDisk('category_icons')
            ->singleFile(); // لأن كل فئة لها أيقونة واحدة فقط
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
