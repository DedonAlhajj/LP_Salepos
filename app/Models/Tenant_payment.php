<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant_payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'amount',
        'payment_method',
        'package_id',
        'payment_date',

    ];


}
