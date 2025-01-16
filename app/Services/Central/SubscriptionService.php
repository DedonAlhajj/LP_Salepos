<?php

namespace App\Services\Central;

use Carbon\Carbon;
use App\Models\{Package, SuperUser, Tenant, TenantPayment};

class SubscriptionService
{
    public function handleSubscription($pendingUser,$data)
    {
        $newPackage = $pendingUser->package;
        $currentSuperUser = SuperUser::with('tenant')->where('email', $data[0])->firstOrFail();
        $tenant = $currentSuperUser->tenant;
        $currentPackage = $tenant->package;

        $tenant->subscription_end = Carbon::parse($tenant->subscription_end);
        $isSubscriptionActive = $tenant->subscription_end && $tenant->subscription_end->isFuture();

        // إذا كانت الباقة نفسها
        if ($currentPackage && $newPackage->id === $currentPackage->id) {
            return $isSubscriptionActive
                ? $this->renewPackage($tenant, $newPackage, $pendingUser, $data)
                : $this->newSubscription($tenant, $newPackage, $pendingUser, $data);
        }

        // إذا كانت الباقة مختلفة
        if ($currentPackage) {
            if ($isSubscriptionActive) {
                return $newPackage->price > $currentPackage->price
                    ? $this->upgradePackage($tenant, $newPackage, $pendingUser, $data)
                    : redirect()->route('Central.packages.index')->withErrors([
                        'message' => 'Cannot downgrade to a lower package while your subscription is active.',
                    ]);
            }

            return $this->newSubscription($tenant, $newPackage, $pendingUser, $data);
        }

        // إذا لم يكن هناك باقة حالية
        return $this->newSubscription($tenant, $newPackage, $pendingUser, $data);
    }

    private function renewPackage($tenant,$package,$pendingUser,$response)
    {
        $subscriptionStart = $tenant->subscription_end;
        $subscriptionEnd = $this->calculateSubscriptionEnd($package, $subscriptionStart);

        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();

        $this->recordPayment($tenant, $package, $response);
        $pendingUser->delete();

        return true;
    }

    private function upgradePackage($tenant,$newPackage,$pendingUser,$response)
    {
        $currentPackage = $tenant->package;
        $remainingDays = $tenant->subscription_end->diffInDays(now());
        $remainingValue = ($currentPackage->price / $currentPackage->duration) * $remainingDays;
        $upgradeCost = $newPackage->price - $remainingValue;

        $subscriptionStart = now();
        $subscriptionEnd = $this->calculateSubscriptionEnd($newPackage, $subscriptionStart);

        $tenant->package_id = $newPackage->id;
        $tenant->subscription_start = $subscriptionStart;
        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();

        $this->recordPayment($tenant, $newPackage, $response, $upgradeCost);
        $pendingUser->delete();

        return true;
    }

    private function newSubscription($tenant,$newPackage,$pendingUser,$response)
    {
        $subscriptionStart = now();
        $subscriptionEnd = $this->calculateSubscriptionEnd($newPackage, $subscriptionStart);

        $tenant->package_id = $newPackage->id;
        $tenant->subscription_start = $subscriptionStart;
        $tenant->subscription_end = $subscriptionEnd;
        $tenant->save();

        $this->recordPayment($tenant, $newPackage, $response);
        $pendingUser->delete();

        return true;
    }

    private function recordPayment(Tenant $tenant, Package $package, array $response, float $amount = null)
    {
        TenantPayment::create([
            'tenant_id' => $tenant->id,
            'amount' => $amount ?? $package->price,
            'package_id' => $package->id,
            'currency' => $response['currency'] ?? 'USD',
            'payment_gateway' => $response['payment_gateway'] ?? 'Unknown',
            'transaction_id' => $response['transaction_id'] ?? 'N/A',
            'reference_number' => $response['reference_number'] ?? 'N/A',
            'status' => 'completed',
            'payment_date' => now(),
        ]);
    }

    private function calculateSubscriptionEnd(Package $package, Carbon $start)
    {
        return $start->copy()->addDays($package->duration);
    }
}

