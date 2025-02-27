<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ExpenseCategory extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[
        "code", "name"
    ];

    public function expense() {
    	return $this->hasMany('App\Models\Expense');
    }
}
