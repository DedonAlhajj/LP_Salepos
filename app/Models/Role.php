<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant; // استيراد الباكج الخاص بالمستأجرين

class Role extends SpatieRole
{
    use BelongsToTenant; // إضافة السمة لدعم تعدد المستأجرين

    /**
     * Scope لجلب الأدوار النشطة فقط.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }
}
