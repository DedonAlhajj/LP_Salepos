<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Deposit extends Model
{
    use BelongsToTenant;
    protected $fillable =[
        "tenant_id",
        "amount", "customer_id", "user_id", "note"
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
