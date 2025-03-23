<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Currency extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable = ["name", "code", "exchange_rate"];
}
