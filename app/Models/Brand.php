<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Brand extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable =[

        "title", "image", "page_title", "short_description", "slug", "is_active"
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brands')
            ->useDisk('brands'); // تحديد القرص الخاص بهذا الموديل
    }
    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
}
