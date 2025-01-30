<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function customers()
    {
        return $this->belongsToMany('App\Models\Customer', 'discount_plan_customers');
    }
}
