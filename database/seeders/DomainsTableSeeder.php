<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain;
use App\Models\Tenant;

class DomainsTableSeeder extends Seeder
{
    public function run()
    {
        $tenants = Tenant::pluck('id')->toArray();

        Domain::factory()->count(20)->create([
            'tenant_id' => fn() => $tenants[array_rand($tenants)],
        ]);
    }
}
