<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\TenantPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
    Display the list of options with the selection.
     */

    public function showPaymentForm(Request $request)
    {
        // التحقق من وجود بيانات التسجيل في الجلسة
        $registrationData = session()->get('registration_data');

        if (!$registrationData) {
            return redirect()->route('Central.packages.index')
                ->withErrors('Please complete your registration before proceeding to payment.');
        }

        // عرض صفحة اختيار طريقة الدفع
        return view('Central.payment.choose', compact('registrationData'));
    }


    public function index(Request $request)
    {
        // Specify custom values for filtering
        $filters = $request->only(['status', 'paying_method', 'start_date', 'end_date']);

        $payments = TenantPayment::with(['tenant', 'package'])
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['paying_method'] ?? null, function ($query, $payingMethod) {
                $query->where('paying_method', $payingMethod);
            })
            ->when(
                isset($filters['start_date'], $filters['end_date']),
                function ($query) use ($filters) {
                    $query->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']]);
                }
            )
            ->paginate(10);

        return view('Central.payments.index', [
            'payments' => $payments,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the details of the selected payment.
     */
    public function show(int $id)
    {
        $payment = TenantPayment::with(['tenant', 'package'])->findOrFail($id);

        return view('Central.payments.show', [
            'payment' => $payment,
        ]);
    }

    /**
     Update payment status.
     */
    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment = TenantPayment::findOrFail($id);
        $payment->update(['status' => $validated['status']]);

        return redirect()
            ->route('payments.index')
            ->with('success', 'The effective payment has been updated.');
    }
}
