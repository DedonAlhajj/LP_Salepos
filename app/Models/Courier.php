<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Courier extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable = ["name", "phone_number", "address", "is_active"];
}
