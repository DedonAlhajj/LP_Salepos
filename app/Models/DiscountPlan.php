<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class DiscountPlan extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable = ['name'];

    public function customers()
    {
        return $this->belongsToMany('App\Models\Customer', 'discount_plan_customers');
    }
}
