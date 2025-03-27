<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    protected $fillable = ['reference_no', 'courier_id', 'status', 'packing_slip_list',
        'amount_list', 'cash_list', 'cheque_list', 'online_payment_list',
        'delivery_charge_list', 'status_list', 'closing_date', 'created_by_id', 'closed_by_id', 'created_at'];


    protected $casts = [
        'packing_slip_list' => 'array',
        'amount_list' => 'array',
        'cash_list' => 'array',
        'delivery_charge_list' => 'array',
        'closing_date' => 'datetime',
    ];

    public function scopeFilter($query, $courier_id, $status)
    {
        return $query
            ->when($courier_id !== 'All Courier', fn($q) => $q->where('courier_id', $courier_id))
            ->when($status, fn($q) => $q->where('status', $status));
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_id');
    }

    public function packingSlips()
    {
        return $this->hasMany(PackingSlip::class, 'id', 'packing_slip_list');
    }

}
