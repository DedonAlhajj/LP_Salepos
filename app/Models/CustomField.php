<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CustomField extends Model
{
    use BelongsToTenant;
    use HasFactory;
    protected $fillable = [
        'entity_type', 'name', 'type', 'default_value', 'option_value', 'grid_value',
        'is_table', 'is_invoice', 'is_required', 'is_admin', 'is_disable', 'tenant_id'
    ];

    // تحويل البيانات إذا كانت من نوع JSON (الخيارات والشبكة)
    protected $casts = [
        'option_value' => 'array',
        'grid_value' => 'array',
    ];

    // العلاقة مع قيم الحقل المخصص
    public function values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    // العلاقة مع الكائنات المرتبطة (مثل Customer أو Product)
    public function entity()
    {
        return $this->morphTo();
    }
}
