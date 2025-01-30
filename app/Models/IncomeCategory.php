<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable =[
        "code", "name", "is_active"
    ];

    public function expense() {
    	return $this->hasMany(Expense::class);
    }
}
