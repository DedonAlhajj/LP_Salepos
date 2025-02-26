<?php

namespace App\Services\Tenant;

use App\Models\Payment;
use App\Models\PosSetting;
use App\Models\Purchase;
use App\Services\Tenant\PaymentStrategies\PaymentStrategyFactory;
use App\Services\Tenant\PaymentStrategies\PaymentStrategyInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{

    /** Add Payment Fun*/
    public function processPayment(array $data)
    {
        $purchaseService = app(PurchaseService::class);
        // التحقق من صحة بيانات الدفع
        $this->validatePaymentData($data);

        // جلب بيانات الشراء
        $purchase = $purchaseService->getPurchase($data['purchase_id']);

        // حساب طريقة الدفع عبر Strategy
        $paymentStrategy = PaymentStrategyFactory::create($data['paid_by_id']);

        // إعداد الدفع كـ Transaction لضمان الأمان
        return DB::transaction(function () use ($data, $purchase, $paymentStrategy,$purchaseService) {
            // تحديث المبلغ المدفوع وحالة الدفع
            $purchaseService->updatePurchasePaymentStatus($purchase, $data['amount']);

            // إضافة payment_id إلى البيانات
            $data['payment_id'] = $this->savePayment($data, $purchase, $paymentStrategy);

            // معالجة الدفع باستخدام الاستراتيجية المناسبة
            $paymentStrategy->process($data);

            return 'Payment processed successfully';
        });
    }

    private function validatePaymentData(array $data)
    {
        if (!isset($data['purchase_id'], $data['amount'], $data['paid_by_id'], $data['account_id'])) {
            throw new Exception("بيانات الدفع غير مكتملة");
        }
    }

    private function savePayment(array $data, Purchase $purchase, PaymentStrategyInterface $paymentStrategy): int
    {
        // إعداد مرجع الدفع
        $data['payment_reference'] = $this->generatePaymentReference();

        // حفظ البيانات في جدول الدفع باستخدام الاستراتيجية المناسبة
        $payment = new Payment();
        $payment->user_id = Auth::id();
        $payment->purchase_id = $purchase->id;
        $payment->account_id = $data['account_id'];
        $payment->payment_reference = $data['payment_reference'];
        $payment->amount = $data['amount'];
        $payment->change = $data['paying_amount'] - $data['amount'];
        $payment->paying_method = $paymentStrategy->getPaymentMethod();
        $payment->payment_note = $data['payment_note'];

        $payment->save();

        return $payment->id;
    }

    private function generatePaymentReference(): string
    {
        return 'ppr-' . now()->format('Ymd-His') . '-' . uniqid();
    }


    /** Update Payment Fun */
    public function updatePayment(array $data)
    {
        $purchaseService = app(PurchaseService::class);

        // التحقق من صحة بيانات الدفع
        $this->validatePaymentDataUpdata($data);

        return DB::transaction(function () use ($data, $purchaseService) {
            // جلب بيانات الدفع والشراء
            $payment = Payment::findOrFail($data['payment_id']);
            $purchase = $purchaseService->getPurchase($payment->purchase_id);

            // حساب الفرق في المبلغ
            $amountDiff = $payment->amount - $data['edit_amount'];

            // تحديث بيانات الشراء
            $purchaseService->updatePurchasePaymentStatus($purchase, -$amountDiff);

            // تحديث بيانات الدفع
            $this->updatePaymentData($payment, $data);

            // استخدام `PaymentStrategy` لمعالجة الدفع
            $paymentStrategy = PaymentStrategyFactory::create($data['edit_paid_by_id']);
            $paymentStrategy->processUpdate($data);

            return 'Payment updated successfully';
        });
    }

    private function validatePaymentDataUpdata(array $data)
    {
        if (!isset($data['payment_id'], $data['edit_amount'], $data['edit_paid_by_id'], $data['account_id'])) {
            throw new Exception("بيانات الدفع غير مكتملة");
        }
    }

    private function updatePaymentData(Payment $payment, array $data)
    {
        $payment->account_id = $data['account_id'];
        $payment->amount = $data['edit_amount'];
        $payment->change = $data['edit_paying_amount'] - $data['edit_amount'];
        $payment->payment_note = $data['edit_payment_note'];
        $payment->paying_method = PaymentStrategyFactory::create($data['edit_paid_by_id'])->getPaymentMethod();
        $payment->save();
    }


    /** Show Payment*/
    public function getPayment($id)
    {
        return Payment::with(['account', 'cheque'])
            ->where('purchase_id', $id)
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->created_at->format(config('date_format')) . ' ' . $payment->created_at->toTimeString(),
                    'payment_reference' => $payment->payment_reference,
                    'paid_amount' => $payment->amount,
                    'paying_method' => $payment->paying_method,
                    'payment_id' => $payment->id,
                    'payment_note' => $payment->payment_note,
                    'cheque_no' => $payment->paying_method == 'Cheque' ? optional($payment->cheque)->cheque_no : null,
                    'change' => $payment->change,
                    'paying_amount' => $payment->amount + $payment->change,
                    'account_name' => optional($payment->account)->name ?? 'N/A',
                    'account_id' => optional($payment->account)->id ?? 0,
                ];
            });

    }

    /** Delete Payment*/
    public function deletePayment(int $paymentId)
    {
        return DB::transaction(function () use ($paymentId) {
            // جلب بيانات الدفع
            $payment = Payment::findOrFail($paymentId);
            $purchaseService = app(PurchaseService::class);
            $purchase = $purchaseService->getPurchase($payment->purchase_id);

            // تحديث حالة الدفع الخاصة بالشراء
            $purchaseService->updatePurchasePaymentStatus($purchase, -$payment->amount);

            // جلب استراتيجية الدفع بناءً على طريقة الدفع
            $paymentStrategy = PaymentStrategyFactory::createFromPayment($payment);

            // تنفيذ أي عمليات خاصة بحذف الدفع (مثل استرداد الأموال من Stripe)
            $paymentStrategy->delete($payment);

            // حذف سجل الدفع من قاعدة البيانات
            $payment->delete();

            return 'Payment deleted successfully';
        });
    }

    /** Delete Related Payments */
    public function deleteRelatedPayments(Purchase $purchase)
    {
        $payments = Payment::where('purchase_id', $purchase->id)->get();
        $posSetting = PosSetting::latest()->first();

        foreach ($payments as $payment) {
            $paymentStrategy = PaymentStrategyFactory::createFromPayment($payment);
            $paymentStrategy->delete($payment);
            $payment->delete();
        }
    }


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

