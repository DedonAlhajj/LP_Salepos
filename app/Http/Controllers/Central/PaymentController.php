<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\Domain;
use App\Models\Package;
use App\Models\PendingUser;
use App\Models\SuperUser;
use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Models\User;
use App\Notifications\TenantUserCreated;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PaymentController extends Controller
{

    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {

        $this->paymentGateway = $paymentGateway;
    }

    public function choose(Request $request)
    {
        // التحقق من صحة المدخلات
        $validated = $request->validate([
            'token' => 'required',
        ]);

        try {
            // فك تشفير المعرّف
            $pendingUserId = decrypt($validated['token']);

            $pendingUser = PendingUser::findOrFail($pendingUserId);

            if ($pendingUser->expires_at && now()->greaterThan($pendingUser->expires_at)) {
                return redirect()->route('Central.packages.index')
                    ->withErrors('Your session has expired. Please try again.');
            }

            $package = Package::find($pendingUser->package_id);
            if (!$package) {
                return redirect()->route('Central.packages.index')->withErrors('The selected package is invalid.');
            }

            return view('Central.payment.choose', compact('pendingUser', 'package'));
        } catch (\Exception $e) {
            logger()->error("Error loading payment choose page: " . $e->getMessage());

            return redirect()->route('Central.packages.index')
                ->withErrors('An error occurred while processing your request. Please try again.');
        }
    }

    public function renewOrUpgradeProcess(Request $request)
    {
        // **1. التحقق من صحة البيانات المدخلة**
        $validatedData = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'operation_type' => 'required|in:renew,upgrade',
        ]);

        $user = Auth::guard('super_users')->user();
        if (!$user) {
            return redirect()->route('Central.packages.index')->withErrors('User not authenticated.');
        }

        $package = Package::find($validatedData['package_id']);
        if (!$package) {
            return redirect()->route('Central.packages.index')->withErrors('The selected package is invalid or no longer available.');
        }

        // **4. إنشاء أو تحديث المستخدم المؤقت**
        PendingUser::updateOrCreate(
            ['email' => $user->email],
            [
                'name' => htmlspecialchars($user->name),
                'package_id' => $package->id,
                'operation_type' => $validatedData['operation_type'],
                'expires_at' => now()->addHours(24),
            ]
        );

        // **5. تجهيز بيانات الدفع**
        $paymentData = $this->paymentGateway->filterDataThatGoToPaymentGateway(
            htmlspecialchars($user->name),
            htmlspecialchars($user->email),
            (float) $package->price,
            "EGP"
        );
        $paymentData['user_id'] = 90909;
       // dd($paymentData);
        $paymentRequest = Request::create('/dummy', 'POST', $paymentData);
        try {
            // **6. إرسال طلب الدفع**
            $response = $this->paymentGateway->sendPayment($paymentRequest);

            // التحقق من نجاح الطلب وإعادة التوجيه
            if (isset($response['url'])) {
                return redirect($response['url']);
            }
            throw new \Exception('Invalid response from payment gateway.');
        } catch (\Exception $e) {
            logger()->error("Payment failed: " . $e->getMessage());
            return redirect()->route('Central.packages.index')->withErrors('An error occurred during the payment process. Please try again.');
        }
    }

    public function paymentProcess(Request $request)
    {

        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'pending_user_id' => 'required|exists:pending_users,id',
            'payment_method' => 'required', // تحديد طرق الدفع المسموح بها
        ]);
        // استرداد المستخدم المؤقت مع الحزمة المرتبطة
        $pendingUser = PendingUser::with('package')->findOrFail($validated['pending_user_id']);


        // التحقق من الحزمة المرتبطة بالمستخدم
        $package = $pendingUser->package;

        if (!$package) {
            return redirect()->route('Central.packages.index')->withErrors('The selected package is invalid or no longer available.');
        }

        $paymentData = $this->paymentGateway->filterDataThatGoToPaymentGateway($pendingUser->name, $pendingUser->email, $package->price, "EGP");

        $paymentRequest = Request::create('/dummy', 'POST', $paymentData);
        //dd($paymentRequest);
        try {
            // إرسال البيانات إلى بوابة الدفع
            $response = $this->paymentGateway->sendPayment($paymentRequest);
            // إعادة التوجيه إلى بوابة الدفع
            return redirect($response['url']);
        } catch (\Exception $e) {
            logger()->error("Payment failed: " . $e->getMessage());
            return redirect()->route('Central.packages.index')->withErrors('An error occurred during the payment process. Please try again.');
        }
    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // معالجة رد الدفع من بوابة الدفع
            $response = $this->paymentGateway->callBack($request);
            $data = $this->paymentGateway->dataThatCameFromPaymentGateway($response);

            if (!$response || !isset($response['success'])) {
                throw new \Exception('Invalid payment gateway response.');
            }

            // جلب المستخدم المؤقت مع الحزمة
            $pendingUser = PendingUser::with('package')->where('email', $data[0])->firstOrFail();

            // التحقق من نجاح العملية
            if ($response['success']) {
                // اختيار العملية بناءً على النوع
                $this->handleOperation($pendingUser, $data);
                return redirect()->route('payment.success');
            }

            // فشل العملية
            return redirect()->route('payment.failed');
        } catch (\Exception $e) {
            logger()->error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('payment.failed')->withErrors('An error occurred during payment processing.');
        }
    }

    protected function handleOperation($pendingUser, $data)
    {
        switch ($pendingUser->operation_type) {
            case 'purchase':
                $this->handlePurchase($pendingUser, $data);
                break;

            case 'renew':
                $this->handleSubscribe($pendingUser, $data);
                break;

            default:
                throw new \InvalidArgumentException('Invalid operation type.');
        }
    }

    protected function handlePurchase($pendingUser, $response)
    {

        DB::beginTransaction();
        try {
            // إنشاء سجل في جدول super_users
            $superUser = SuperUser::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password, // تأكد من أن كلمة المرور مشفرة
            ]);

            event(new Registered($superUser));
            Auth::guard('super_users')->login($superUser);
            // استرداد معلومات الحزمة
            $package = $pendingUser->package;

            // حساب تواريخ الاشتراك
            $subscriptionStart = now();
            $subscriptionEnd = $this->calculateSubscriptionEnd($package, $subscriptionStart);

            // إنشاء سجل في جدول tenants
            $tenant = Tenant::create([
                'super_user_id' => $superUser->id,
                'name' => $pendingUser->store_name,
                'package_id' => $package->id,
                'subscription_start' => $subscriptionStart,
                'subscription_end' => $subscriptionEnd,
            ]);
            //$package->is_trial ? now()->addDays(config('app.trial_duration', 14)) : null

            $tenant->domains()->create([
                'domain' => $this->formatDomain($pendingUser->domain),
            ]);

            TenantPayment::create([
                'tenant_id' => $tenant->id,
                'amount' => $package->price,
                'package_id' => $package->id,
                'currency' => $response[1] ?? 'USD',
                'payment_gateway' => $response[2] ?? 'Unknown',
                'transaction_id' => $response[3] ?? 'N/A',
                'reference_number' => $response[4] ?? 'N/A',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            // حذف المستخدم المؤقت
            $pendingUser->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Purchase operation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function calculateSubscriptionEnd($package, $start)
    {
        return match ($package->duration_unit) {
            'days' => $start->copy()->addDays($package->duration),
            'weeks' => $start->copy()->addWeeks($package->duration),
            'months' => $start->copy()->addMonths($package->duration),
            'year' => $start->copy()->addYears($package->duration),
            default => throw new \InvalidArgumentException('Invalid duration unit.'),
        };
    }

    private function formatDomain($domain)
    {
        return $domain .'.'. env('SESSION_DOMAIN_CENTRAL', '');
    }

    public function handleSubscribe($pendingUser, $data)
    {

        $newPackage = $pendingUser->package;
        $currentSuperUser = SuperUser::with('tenant')->where('email', $data[0])->firstOrFail();

        $tenant = $currentSuperUser->tenant;
        $currentPackage = $tenant->package;
        //dd($currentPackage);

        $tenant->subscription_end = Carbon::parse($tenant->subscription_end);
        $isSubscriptionActive = $tenant->subscription_end && $tenant->subscription_end->isFuture();


        // إذا كانت الباقة نفسها
        if ($currentPackage && $newPackage->id === $currentPackage->id) {
            if ($isSubscriptionActive) {
                // تجديد الباقة
                return $this->renewPackage($tenant, $newPackage,$pendingUser,$data);
            } else {
                // اشتراك جديد (تجديد بعد انتهاء الاشتراك)
                return $this->newSubscription($tenant, $newPackage,$pendingUser,$data);
            }
        }

        // إذا كانت الباقة مختلفة
        if ($currentPackage) {
            if ($isSubscriptionActive) {
                // التحقق إذا كانت الباقة الجديدة أعلى
                if ($newPackage->price > $currentPackage->price) {
                    return $this->upgradePackage($tenant, $newPackage,$pendingUser,$data);
                } else {
                    return redirect()->route('Central.packages.index')->withErrors([
                        'message' => 'Cannot downgrade to a lower package will your Subscription does not end. Please choose a higher package to upgrade.',
                    ]);
                }
            } else {
                // اشتراك جديد إذا كان الاشتراك منتهيًا
                return $this->newSubscription($tenant, $newPackage,$pendingUser,$data);
            }
        }

        // اشتراك جديد إذا لم يكن هناك باقة حالية
        return $this->newSubscription($tenant, $newPackage,$pendingUser,$data);
    }

    protected function renewPackage(Tenant $tenant, Package $package,$pendingUser,$response)
    {
        $subscriptionStart = $tenant->subscription_end;
        $subscriptionEnd = $this->calculateSubscriptionEnd($package, $subscriptionStart);

        // تحديث تاريخ نهاية الاشتراك
        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();

        // تسجيل الدفع
        TenantPayment::create([
            'tenant_id' => $tenant->id,
            'amount' => $package->price,
            'package_id' => $package->id,
            'currency' => $response[1] ?? 'USD',
            'payment_gateway' => $response[2] ?? 'Unknown',
            'transaction_id' => $response[3] ?? 'N/A',
            'reference_number' => $response[4] ?? 'N/A',
            'status' => 'completed',
            'payment_date' => now(),
        ]);
        // حذف المستخدم المؤقت
        $pendingUser->delete();
        return true;
    }

    protected function upgradePackage(Tenant $tenant, Package $newPackage,$pendingUser,$response)
    {
        $currentPackage = $tenant->package;

        // حساب القيمة المتبقية
        $remainingDays = $tenant->subscription_end->diffInDays(now());
        $remainingValue = ($currentPackage->price / $currentPackage->duration) * $remainingDays;
        $upgradeCost = $newPackage->price - $remainingValue;

        // تسجيل الدفع
        TenantPayment::create([
            'tenant_id' => $tenant->id,
            'amount' => $upgradeCost,
            'package_id' => $newPackage->id,
            'currency' => $response[1] ?? 'USD',
            'payment_gateway' => $response[2] ?? 'Unknown',
            'transaction_id' => $response[3] ?? 'N/A',
            'reference_number' => $response[4] ?? 'N/A',
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        // تحديث الباقة وتاريخ الاشتراك
        $subscriptionStart = now();
        $subscriptionEnd = $this->calculateSubscriptionEnd($newPackage, $subscriptionStart);

        $tenant->package_id = $newPackage->id;
        $tenant->subscription_start = $subscriptionStart;
        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();
        // حذف المستخدم المؤقت
        $pendingUser->delete();
        return true;

    }

    protected function newSubscription(Tenant $tenant, Package $newPackage,$pendingUser,$response)
    {
        $subscriptionStart = now();
        $subscriptionEnd = $this->calculateSubscriptionEnd($newPackage, $subscriptionStart);

        // تحديث الباقة وتاريخ الاشتراك
        $tenant->package_id = $newPackage->id;
        $tenant->subscription_start = $subscriptionStart;
        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();

        // تسجيل الدفع
        TenantPayment::create([
            'tenant_id' => $tenant->id,
            'amount' => $newPackage->price,
            'package_id' => $newPackage->id,
            'currency' => $response[1] ?? 'USD',
            'payment_gateway' => $response[2] ?? 'Unknown',
            'transaction_id' => $response[3] ?? 'N/A',
            'reference_number' => $response[4] ?? 'N/A',
            'status' => 'completed',
            'payment_date' => now(),
        ]);
        // حذف المستخدم المؤقت
        $pendingUser->delete();

        return true;
    }

    public function success()
    {

        return view('Central.payment.massage_error.payment-success');
    }

    public function failed()
    {

        return view('Central.payment.massage_error.payment-failed');
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

    public function show(int $id)
    {
        $payment = TenantPayment::with(['tenant', 'package'])->findOrFail($id);

        return view('Central.payments.show', [
            'payment' => $payment,
        ]);
    }

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
