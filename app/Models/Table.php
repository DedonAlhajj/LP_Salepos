<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Table extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable = ['name', 'number_of_person', 'description'];
}
