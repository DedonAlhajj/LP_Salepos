<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    protected $fillable = ['reference_no', 'courier_id', 'status', 'packing_slip_list',
        'amount_list', 'cash_list', 'cheque_list', 'online_payment_list',
        'delivery_charge_list', 'status_list', 'closing_date', 'created_by_id', 'closed_by_id', 'created_at'];


    public function courier()
    {
        return $this->belongsTo('App\Models\Courier');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by_id');
    }

    public function closedBy()
    {
        return $this->belongsTo('App\Models\User', 'closed_by_id');
    }
}
