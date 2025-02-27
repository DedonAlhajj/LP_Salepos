<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class IncomeCategory extends Model
{
    use HasFactory;

    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable =[
        "code", "name"
    ];

    public function expense() {
    	return $this->hasMany(Expense::class);
    }
}
