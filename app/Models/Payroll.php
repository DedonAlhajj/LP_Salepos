<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable =[
        "reference_no", "employee_id", "account_id", "user_id",
        "amount", "paying_method", "note", "created_at"
    ];

    public function employee()
    {
    	return $this->belongsTo('App\Models\Employee');
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class,'account_id');
    }
}
