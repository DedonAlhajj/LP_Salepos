<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Biller extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[
        "name", "image", "company_name", "vat_number",
        "email", "phone_number", "address", "city",
        "state", "postal_code", "country", "is_active"
    ];



    public function sale()
    {
    	return $this->hasMany('App\Models\Sale');
    }
}
