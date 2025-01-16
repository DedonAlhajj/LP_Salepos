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
use App\Services\Central\PaymentService;
use App\Services\Central\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PaymentController extends Controller
{

    protected PaymentGatewayInterface $paymentGateway;
    protected PaymentService $paymentService;
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService,PaymentGatewayInterface $paymentGateway,PaymentService $paymentService)
    {
        $this->paymentGateway = $paymentGateway;
        $this->paymentService = $paymentService;
        $this->subscriptionService = $subscriptionService;
    }

    public function choose(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
        ]);

        try {
            $pendingUser = $this->paymentService->getPendingUserFromToken($validated['token']);

            if ($pendingUser->isExpired()) {
                return redirect()->route('Central.packages.index')
                    ->withErrors('Your session has expired. Please try again.');
            }

            $package = $pendingUser->package;

            if (!$package || !$package->is_active) {
                return redirect()->route('Central.packages.index')->withErrors('The selected package is invalid.');
            }

            return view('Central.payment.choose', compact('pendingUser', 'package'));
        } catch (\Exception $e) {
            Log::error("Error loading payment choose page: " . $e->getMessage());
            return redirect()->route('Central.packages.index')
                ->withErrors('An error occurred while processing your request. Please try again.');
        }
    }


    public function renewOrUpgradeProcess(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'operation_type' => 'required|in:renew,upgrade',
        ]);

        $user = Auth::guard('super_users')->user();
        if (!$user) {
            return redirect()->route('Central.packages.index')->withErrors('User not authenticated.');
        }

        try {
            $response = $this->paymentService->processRenewOrUpgrade($user, $validated);

            return redirect($response['url']);
        } catch (\Exception $e) {
            Log::error("Renew or upgrade process failed: " . $e->getMessage());
            return redirect()->route('Central.packages.index')
                ->withErrors('An error occurred during the process. Please try again.');
        }
    }


    public function paymentProcess(Request $request)
    {
        $validated = $request->validate([
            'pending_user_id' => 'required|exists:pending_users,id',
            'payment_method' => 'required',
        ]);

        try {
            $response = $this->paymentService->processPayment($validated);

            return redirect($response['url']);
        } catch (\Exception $e) {
            Log::error("Payment process failed: " . $e->getMessage());
            return redirect()->route('Central.packages.index')
                ->withErrors('An error occurred during the payment process. Please try again.');
        }
    }


    public function callBack(Request $request)
    {
        try {
            $response = $this->paymentGateway->callBack($request);
            $data = $this->paymentGateway->dataThatCameFromPaymentGateway($response);

            if (!$response['success']) {
                throw new \Exception('Invalid payment gateway response.');
            }

            $pendingUser = PendingUser::with('package')->where('email', $data[0])->firstOrFail();

            return match ($pendingUser->operation_type) {
                'purchase' => $this->handlePurchase($pendingUser, $data),
                'renew' => $this->handleSubscribe($pendingUser, $data),
                default => throw new \Exception('Invalid operation type.'),
            };
        } catch (\Exception $e) {
            logger()->error('Callback error: ' . $e->getMessage());
            return redirect()->route('payment.failed')->withErrors('An error occurred during payment processing.');
        }
    }


    private function handlePurchase($pendingUser,$response)
    {
        $user = $this->paymentService->handlePurchase($pendingUser,$response);
        $token = $user->createToken('Payment Success Token')->plainTextToken;

        return redirect()->route('payment.success', ['token' => $token]);
    }


    private function handleSubscribe($pendingUser,$data)
    {
        // استدعاء خدمة الاشتراكات لمعالجة التجديد أو الترقية
        $this->subscriptionService->handleSubscription($pendingUser,$data);
        return redirect()->route('payment.success');
    }


    protected function getUserFromToken($token)
    {
        // إذا كنت تستخدم Sanctum:
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;

        return $user;
    }


    public function success(Request $request)
    {
        if ($request->has('token')) {
            //dd('jfisdjfisdf');

            $token = $request->query('token');
            // استخراج بيانات المستخدم باستخدام التوكن (اعتمادًا على طريقة المصادقة لديك)
            $user = $this->getUserFromToken($token);
            if ($user) {
                $superUser = SuperUser::find($user->id);

                Auth::guard('super_users')->login($superUser);
            }
        }
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


        return view('Central.payment.index', [
            'payments' => $payments,
            'filters' => $filters,
        ]);
    }


    public function show(int $id)
    {
        $payment = TenantPayment::with(['tenant', 'package'])->findOrFail($id);

        return view('Central.payment.show', [
            'payment' => $payment,
        ]);


    }
}
