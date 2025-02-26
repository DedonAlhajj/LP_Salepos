<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;

class PaymentStrategyFactory
{
    public static function create(int $paidById): PaymentStrategyInterface
    {
        switch ($paidById) {
            case 1:
                return new CashPaymentStrategy();
            case 2:
                return new GiftCardPaymentStrategy();
            case 3:
                return new CreditCardPaymentStrategy();
            case 4:
                return new ChequePaymentStrategy();
            default:
                throw new \Exception("Payment method not supported.");
        }
    }

    public static function createFromPayment(Payment $payment): PaymentStrategyInterface
    {
        return match ($payment->paying_method) {
            'Credit Card' => new CreditCardPaymentStrategy(),
            'Cheque' => new ChequePaymentStrategy(),
            default => throw new \Exception("Payment method not supported."),
        };
    }
}
