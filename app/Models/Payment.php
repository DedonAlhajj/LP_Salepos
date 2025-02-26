<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Payment extends Model
{
    use BelongsToTenant;
    protected $fillable =[
        "tenant_id",
        "purchase_id", "user_id", "sale_id", "cash_register_id", "account_id",
        "payment_receiver", "payment_reference", "amount", "used_points", "change", "paying_method", "payment_note"
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function cheque()
    {
        return $this->hasOne(PaymentWithCheque::class, 'payment_id');
    }
}
