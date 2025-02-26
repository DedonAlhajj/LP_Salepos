<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;

class CashPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $data)
    {
        // لا توجد تفاصيل إضافية لهذا النوع من الدفع
        // فقط يتم حفظ البيانات في جدول Payment.
    }

    public function getPaymentMethod(): string
    {
        return 'Cash';
    }

    public function processUpdate(array $data)
    {
        // TODO: Implement processUpdate() method.
    }

    public function delete(Payment $payment)
    {
        // TODO: Implement delete() method.
    }
}
