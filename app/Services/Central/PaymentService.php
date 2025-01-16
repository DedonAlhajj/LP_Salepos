<?php

namespace App\Services\Central;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Support\Facades\Crypt;
use App\Models\{PendingUser, Package, SuperUser, Tenant, TenantPayment};
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * الحصول على المستخدم المؤقت من الرمز المشفر.
     */
    public function getPendingUserFromToken(string $token): PendingUser
    {
        $pendingUserId = Crypt::decrypt($token);
        return PendingUser::with('package')->findOrFail($pendingUserId);
    }

    /**
     * معالجة تجديد أو ترقية الحزمة.
     */
    public function processRenewOrUpgrade($user, array $data)
    {
        $package = Package::findOrFail($data['package_id']);

        PendingUser::updateOrCreate(
            ['email' => $user->email],
            [
                'name' => htmlspecialchars($user->name),
                'package_id' => $package->id,
                'operation_type' => $data['operation_type'],
                'expires_at' => now()->addHours(24),
            ]
        );

        $paymentData = $this->paymentGateway->filterDataThatGoToPaymentGateway(
            htmlspecialchars($user->name),
            htmlspecialchars($user->email),
            (float) $package->price,
            "EGP"
        );

        $paymentData['user_id'] = $user->id;
        $paymentRequest = request()->create('/dummy', 'POST', $paymentData);

        return $this->paymentGateway->sendPayment($paymentRequest);
    }

    /**
     * معالجة عملية الدفع.
     */
    public function processPayment(array $data)
    {
        $pendingUser = PendingUser::with('package')->findOrFail($data['pending_user_id']);
        $package = $pendingUser->package;

        if (!$package) {
            throw new \Exception('The selected package is invalid.');
        }

        $paymentData = $this->paymentGateway->filterDataThatGoToPaymentGateway(
            $pendingUser->name,
            $pendingUser->email,
            $package->price,
            "EGP"
        );

        $paymentRequest = request()->create('/dummy', 'POST', $paymentData);

        return $this->paymentGateway->sendPayment($paymentRequest);
    }

    public function handlePurchase($pendingUser,$response): SuperUser
    {
        DB::beginTransaction();
        try {
            // إنشاء المستخدم في جدول super_users
            $superUser = SuperUser::create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password, // تشفير كلمة المرور
            ]);

            $package = $pendingUser->package;
            $subscriptionStart = now();
            $subscriptionEnd = $this->calculateSubscriptionEnd($package, $subscriptionStart);

            // إنشاء المستأجر
            $tenant = Tenant::create([
                'super_user_id' => $superUser->id,
                'name' => $pendingUser->store_name,
                'package_id' => $package->id,
                'subscription_start' => $subscriptionStart,
                'subscription_end' => $subscriptionEnd,
            ]);

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

            $pendingUser->delete();

            DB::commit();
            return $superUser;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Purchase operation failed: " . $e->getMessage());
        }
    }

    private function calculateSubscriptionEnd($package, $start)
    {
        return match ($package->duration_unit) {
            'days' => $start->addDays($package->duration),
            'weeks' => $start->addWeeks($package->duration),
            'months' => $start->addMonths($package->duration),
            'years' => $start->addYears($package->duration),
            default => throw new Exception('Invalid duration unit.'),
        };
    }

    private function formatDomain($domain)
    {
        return "{$domain}." . env('SESSION_DOMAIN_CENTRAL', 'example.com');
    }

}
