<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Account extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[
        "tenant_id",
        "account_no", "name", "initial_balance", "total_balance", "note", "is_default", "is_active"
    ];
}
