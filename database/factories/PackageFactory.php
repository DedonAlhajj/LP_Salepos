<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    public function definition()
    {
        return [
            'package_name' => $this->faker->word,
            'duration' => $this->faker->numberBetween(1, 12),
            'duration_unit' => $this->faker->randomElement(['days', 'weeks', 'months', 'year']),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'description' => $this->faker->sentence,
            'max_users' => $this->faker->numberBetween(5, 100),
            'max_storage' => $this->faker->randomNumber(2) . ' GB',
            'is_active' => $this->faker->randomElement(['0', '1']),
            'is_trial' => $this->faker->randomElement(['0', '1']),
        ];
    }
}
