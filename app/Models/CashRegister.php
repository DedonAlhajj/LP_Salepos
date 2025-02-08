<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CashRegister extends Model
{
    use BelongsToTenant;

    protected $fillable =[
        "tenant_id","cash_in_hand", "user_id", "warehouse_id", "status"];

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse');
    }
}
