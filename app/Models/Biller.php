<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Biller extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;
    protected $fillable =[
        "name", "image", "company_name", "vat_number",
        "email", "phone_number", "address", "city",
        "state", "postal_code", "country","tenant_id",
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('biller')
            ->useDisk('billers_media'); // تحديد القرص الخاص بهذا الموديل
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($biller) {
            $biller->clearMediaCollection('biller');
        });
    }

    public function sale()
    {
    	return $this->hasMany('App\Models\Sale');
    }
}
