<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeaturesTableSeeder extends Seeder
{
    public function run()
    {
        Feature::factory()->count(10)->create();
    }
}

