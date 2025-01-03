<?php

namespace Database\Seeders;

use App\Models\Feature_package;
use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\Package;

class FeaturesPackagesTableSeeder extends Seeder
{
    public function run()
    {
        $features = Feature::pluck('id')->toArray();
        $packages = Package::pluck('id')->toArray();

        foreach ($packages as $package) {
            foreach (array_rand($features, 3) as $feature) {
                Feature_package::create([
                    'feature_id' => $features[$feature],
                    'package_id' => $package,
                ]);
            }
        }
    }
}

