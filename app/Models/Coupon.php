<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Coupon extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable =[
        "name","code", "type", "amount", "minimum_amount", "user_id", "quantity", "used", "expired_date"
    ];
}
