<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TenantPayment;
use App\Models\Tenant;
use App\Models\Package;

class TenantPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $tenants = Tenant::pluck('id')->toArray();
        $packages = Package::pluck('id')->toArray();

        TenantPayment::factory()->count(15)->create([
            'tenant_id' => fn() => $tenants[array_rand($tenants)],
            'package_id' => fn() => $packages[array_rand($packages)],
        ]);
    }
}

