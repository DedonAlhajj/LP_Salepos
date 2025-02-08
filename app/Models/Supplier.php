<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Supplier extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable =[
        "tenant_id",
        "name", "company_name", "vat_number",
        "email", "phone_number", "address", "city",
        "state", "postal_code", "country"

    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('supplier')
            ->useDisk('supplier_media'); // تحديد القرص الخاص بهذا الموديل
    }

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
}
