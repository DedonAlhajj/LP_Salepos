<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CustomFieldValue extends Model
{
    use BelongsToTenant;
    // الحقول القابلة للملء
    protected $fillable = [
        'custom_field_id', 'value', 'entity_type', 'entity_id', 'tenant_id'
    ];

    // العلاقة مع الحقل المخصص
    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }

    // العلاقة البوليمورفية مع الكائنات (مثل Customer أو Product)
    public function entity()
    {
        return $this->morphTo();
    }
}
