<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CustomerGroup extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[

        "name", "percentage", "is_active"
    ];


}
