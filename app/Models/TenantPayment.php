<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id', 'amount', 'package_id', 'currency',
        'paying_method', 'transaction_id', 'reference_number',
        'status', 'payment_date'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}




