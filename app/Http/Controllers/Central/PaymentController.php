<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\Package;
use App\Models\TenantPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {

        $this->paymentGateway = $paymentGateway;
    }

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

    public function renewOrUpgradeProcess(Request $request)
    {
        // الحصول على المستخدم الحالي
        $user = auth()->user();

        // التحقق من معرف الحزمة المرسل
        $packageId = $request->input('package_id');
        $package = Package::find($packageId);

        if (!$package) {
            return redirect()->back()->withErrors('Invalid package selected.');
        }

        // إعداد بيانات الدفع
        $paymentData = [
            "CustomerName" => htmlspecialchars($user->name),
            "CustomerEmail" => htmlspecialchars($user->email),
            "InvoiceValue" => (float) $package->price,
            "DisplayCurrencyIso" => "EGP",
            "OperationType" => $request->input('operation_type', 'renew'), // "renew" أو "upgrade"
        ];

        try {
            // إرسال الطلب إلى بوابة الدفع
            $response = $this->paymentGateway->sendPayment($paymentData);

            // إعادة التوجيه إلى بوابة الدفع
            return redirect($response['url']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Payment process failed. Please try again later.');
        }
    }


    public function paymentProcess(Request $request)
    {


        // التحقق من وجود بيانات التسجيل في الجلسة
        $registrationData = session('registration_data');
        if (empty($registrationData)) {
            return redirect()->route('Central.packages.index')
                ->withErrors('Please complete your registration before proceeding to payment.');
        }

        // التحقق من وجود معرف الحزمة والتحقق من الحزمة
        $packageId = $registrationData['package_id'] ?? null;
        $package = $packageId ? Package::find($packageId) : null;

        if (!$package) {
            return redirect()->route('Central.packages.index')
                ->withErrors('Invalid package selected.');
        }

        // التحقق من صحة البيانات (الاسم والبريد الإلكتروني)
        if (empty($registrationData['name']) || empty($registrationData['email']) || !filter_var($registrationData['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('Central.packages.index')
                ->withErrors('Invalid registration data. Please try again.');
        }

        // إعداد البيانات المطلوبة لبوابة الدفع
        $paymentData = [
            "CustomerName" => htmlspecialchars($registrationData['name']),
            "CustomerEmail" => htmlspecialchars($registrationData['email']),
            "InvoiceValue" => (float) $package->price, // تأكيد أن القيمة عدد عشري
            "DisplayCurrencyIso" => "EGP",
            "OperationType" => $request->input('operation_type', 'purchase'),
        ];

        try {
            // إرسال البيانات إلى بوابة الدفع
            $response = $this->paymentGateway->sendPayment($paymentData);

            // إفراغ بيانات الجلسة بعد الاستخدام
           // session()->forget('registration_data');

            // إعادة التوجيه إلى بوابة الدفع
            return redirect($response['url']);
        } catch (\Exception $e) {

            return redirect()->route('Central.packages.index')
                ->withErrors('Payment process failed. Please try again later.');
        }


    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->paymentGateway->callBack($request);
        if ($response) {

            return redirect()->route('payment.success');
        }
        return redirect()->route('payment.failed');
    }

    public function callBack1(Request $request): \Illuminate\Http\RedirectResponse
    {
        // معالجة رد الدفع من بوابة الدفع
        $response = $this->paymentGateway->callBack($request);

        if ($response) {
            // تحديد نوع العملية (افتراضي: purchase)
            $operationType = $request->input('operation_type', 'purchase');

            // تنفيذ العملية بناءً على النوع
            switch ($operationType) {
                case 'purchase':
                    $this->handlePurchase($response);
                    break;

                case 'renew':
                    $this->handleRenewal($response);
                    break;

                case 'upgrade':
                    $this->handleUpgrade($response);
                    break;

                default:
                    return redirect()->route('payment.failed')->withErrors('Invalid operation type.');
            }

            // نجاح العملية
            return redirect()->route('payment.success');
        }

        // فشل العملية
        return redirect()->route('payment.failed');
    }


    protected function handlePurchase($response): void
    {

    }

    protected function handleRenewal($response): void
    {
        /* = TenantPackage::where('user_id', auth()->id())
            ->where('package_id', $response['package_id'])
            ->first();

        if ($tenantPackage) {
            $tenantPackage->update([
                'end_date' => $tenantPackage->end_date->addMonth(),
            ]);
        } else {
            throw new \Exception('No active package found for renewal.');
        }*/
    }

    protected function handleUpgrade($response): void
    {
       /* $newPackageId = $response['package_id'];
        $tenantPackage = TenantPackage::where('user_id', auth()->id())->first();

        if ($tenantPackage) {
            $tenantPackage->update([
                'package_id' => $newPackageId,
                'end_date' => now()->addMonth(), // تعيين تاريخ جديد بناءً على الترقية
            ]);
        } else {
            throw new \Exception('No active package found for upgrade.');
        }*/
    }


    public function success()
    {

        return view('payment-success');
    }
    public function failed()
    {

        return view('payment-failed');
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

return $payments;
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
/*@foreach ($packages as $package)
    <div class="package-card">
        <h3>{{ $package->name }}</h3>
        <p>Price: {{ $package->price }} EGP</p>
        <p>{{ $package->description }}</p>

        @guest
            <!-- زر الشراء لغير المسجلين -->
            <a href="{{ route('register') }}" class="btn btn-primary">شراء</a>
        @else
            @php
                $currentPackage = auth()->user()->tenant->package ?? null;
                $isSamePackage = $currentPackage && $currentPackage->id === $package->id;
                $isExpired = $currentPackage && $currentPackage->end_date < now();
            @endphp

            @if ($isSamePackage && $isExpired)
                <!-- زر التجديد -->
                <form action="{{ route('payment.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="operation_type" value="renew">
                    <button type="submit" class="btn btn-warning">تجديد</button>
                </form>
            @elseif (!$isSamePackage)
                <!-- زر الترقية -->
                <form action="{{ route('payment.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="operation_type" value="upgrade">
                    <button type="submit" class="btn btn-success">ترقية</button>
                </form>
            @endif
        @endguest
    </div>
@endforeach
*/
