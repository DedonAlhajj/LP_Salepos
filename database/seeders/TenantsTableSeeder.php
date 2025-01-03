<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\SuperUser;
use App\Models\Package;

class TenantsTableSeeder extends Seeder
{
    public function run()
    {
        $superUsers = SuperUser::pluck('id')->toArray();
        $packages = Package::pluck('id')->toArray();

        Tenant::factory()->count(10)->create([
            'super_user_id' => fn() => $superUsers[array_rand($superUsers)],
            'package_id' => fn() => $packages[array_rand($packages)],
        ]);
    }
}

