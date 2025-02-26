<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;
use App\Models\PaymentWithCheque;

class ChequePaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $data)
    {
        PaymentWithCheque::create([
            'payment_id' => $data['payment_id'],
            'cheque_no' => $data['cheque_no'],
        ]);
    }

    public function getPaymentMethod(): string
    {
        return 'Cheque';
    }

    public function processUpdate(array $data)
    {
        // جلب بيانات الدفع القديمة
        $payment = Payment::findOrFail($data['payment_id']);

        // تحديث بيانات الشيك إذا كان الدفع بالشيك سابقًا
        $chequePayment = PaymentWithCheque::where('payment_id', $payment->id)->first();
        if ($chequePayment) {
            $chequePayment->cheque_no = $data['edit_cheque_no'];
            $chequePayment->save();
        } else {
            // إنشاء سجل جديد إذا لم يكن موجودًا
            PaymentWithCheque::create([
                'payment_id' => $data['payment_id'],
                'cheque_no' => $data['edit_cheque_no'],
            ]);
        }
    }

    public function delete(Payment $payment)
    {
        PaymentWithCheque::where('payment_id', $payment->id)->delete();
    }
}
