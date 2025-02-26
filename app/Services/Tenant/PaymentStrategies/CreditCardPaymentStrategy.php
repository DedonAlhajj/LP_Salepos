<?php

namespace App\Services\Tenant\PaymentStrategies;

use App\Models\Payment;
use App\Models\PaymentWithCreditCard;
use App\Models\PosSetting;
use Illuminate\Support\Facades\Auth;

class CreditCardPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $data)
    {
//        $lims_pos_setting_data = PosSetting::latest()->first();
//        // الاتصال بـ Stripe لمعالجة الدفع
//        Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
//        $charge = Charge::create([
//            'amount' => $data['amount'] * 100, // تحويل المبلغ إلى سنتات
//            'currency' => 'usd',
//            'source' => $data['stripeToken'],
//        ]);
//
//        // حفظ بيانات الدفع مع بطاقة الائتمان
//        PaymentWithCreditCard::create([
//            'payment_id' => $data['payment_id'],
//            'customer_id' => $data['user_id'],
//            'customer_stripe_id' => $charge->id,
//            'charge_id' => $charge->id,
//        ]);
    }

    public function getPaymentMethod(): string
    {
        return 'Credit Card';
    }

    public function processUpdate(array $data)
    {
//        // جلب بيانات الدفع القديمة
//        $payment = Payment::findOrFail($data['payment_id']);
//
//        // إذا كان الدفع بالبطاقة الائتمانية، يتم استرجاع الدفعة القديمة وإنشاء دفعة جديدة
//        if ($payment->paying_method === 'Credit Card') {
//            $previousPayment = PaymentWithCreditCard::where('payment_id', $payment->id)->first();
//            if ($previousPayment) {
//                \Stripe\Refund::create(['charge' => $previousPayment->charge_id]);
//                $previousPayment->delete();
//            }
//        }
//
//        // إجراء الدفع الجديد
//        Stripe::setApiKey(config('services.stripe.secret'));
//        $charge = \Stripe\Charge::create([
//            'amount' => $data['edit_amount'] * 100,
//            'currency' => 'usd',
//            'source' => $data['stripeToken'],
//        ]);
//
//        // حفظ بيانات الدفع الجديدة
//        PaymentWithCreditCard::create([
//            'payment_id' => $data['payment_id'],
//            'customer_id' => Auth::id(),
//            'customer_stripe_id' => $charge->id,
//            'charge_id' => $charge->id,
//        ]);
    }

    public function delete(Payment $payment)
    {
     /*   $posSetting = PosSetting::latest()->first();
        if (!$posSetting->stripe_secret_key) {
            return;
        }

        \Stripe\Stripe::setApiKey($posSetting->stripe_secret_key);

        // جلب بيانات الدفع من Stripe
        $creditCardPayment = PaymentWithCreditCard::where('payment_id', $payment->id)->first();

        if ($creditCardPayment) {
            \Stripe\Refund::create([
                'charge' => $creditCardPayment->charge_id,
            ]);
            $creditCardPayment->delete();
        }*/
    }
}
