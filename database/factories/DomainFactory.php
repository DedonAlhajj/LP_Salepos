<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;

class DomainFactory extends Factory
{
    public function definition()
    {
        return [
            'domain' => $this->faker->unique()->domainName,
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'type' => $this->faker->randomElement(['subdomain', 'custom']),
            'status' => $this->faker->randomElement(['active', 'expired', 'suspended']),
        ];
    }
}
