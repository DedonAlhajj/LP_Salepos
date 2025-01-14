<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
   public $timestamps = false;
    protected $fillable = [
        'package_name',
        'duration',
        'duration_unit',
        'price',
        'description',
        'max_users',
        'max_storage',
        'is_active',
        'is_trial',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);// زي ماقلنا في كذا موديل ان ده انتي هتنزليه مع الباكدج
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'features_packages');
    }

    public function payments()
    {
        return $this->hasMany(TenantPayment::class);
    }

}
