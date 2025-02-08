<?php

namespace App\Services\Tenant;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function createPayment(?int $saleId, ?int $purchaseId, float $amount, ?int $cashRegisterId, int $accountId, ?string $note)
    {
        Payment::create([
            'payment_reference' => 'spr-' . now()->format('Ymd-His'),
            'sale_id' => $saleId,
            'purchase_id' => $purchaseId,
            'user_id' => Auth::guard('web')->id(),
            'cash_register_id' => $cashRegisterId,
            'account_id' => $accountId,
            'amount' => $amount,
            'change' => 0,
            'paying_method' => 'Cash',
            'payment_note' => $note
        ]);
    }


}

