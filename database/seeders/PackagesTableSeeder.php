<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackagesTableSeeder extends Seeder
{
    public function run()
    {
        Package::factory()->count(5)->create();
    }
}

