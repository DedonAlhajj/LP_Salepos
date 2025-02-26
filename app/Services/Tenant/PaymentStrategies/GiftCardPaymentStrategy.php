<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;

class GiftCardPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $data)
    {

    }

    public function getPaymentMethod(): string
    {
        return "Gift Card";
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
