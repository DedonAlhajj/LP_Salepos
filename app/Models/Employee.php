<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Employee extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('employees')
            ->useDisk('employees')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

    }
    protected $fillable =[
        "name", "image", "department_id", "email", "phone_number",
        "user_id", "staff_id", "address", "city", "country"
    ];

    public function payroll()
    {
    	return $this->hasMany('App\Models\Payroll');
    }

}
