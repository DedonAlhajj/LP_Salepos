<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;

interface PaymentStrategyInterface
{
    public function process(array $data);
    public function processUpdate(array $data);
    public function delete(Payment $payment);
    public function getPaymentMethod(): string;
}
