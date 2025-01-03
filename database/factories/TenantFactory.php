<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SuperUser;
use App\Models\Package;

class TenantFactory extends Factory
{
    public function definition()
    {
        return [
            'super_user_id' => SuperUser::inRandomOrder()->first()->id,
            'name' => $this->faker->company,
            'package_id' => Package::inRandomOrder()->first()->id,
            'subscription_start' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'subscription_end' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_active' => $this->faker->boolean(80), // 80% يكون نشطًا
            'trial_end' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'data' => json_encode([
                'address' => $this->faker->address,
                'phone' => $this->faker->phoneNumber,
            ]),
        ];
    }
}
