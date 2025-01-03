<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;
use App\Models\Package;

class TenantPaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'package_id' => Package::inRandomOrder()->first()->id,
            'currency' => $this->faker->currencyCode,
            'paying_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash', 'check']),
            'transaction_id' => $this->faker->uuid,
            'reference_number' => $this->faker->regexify('[A-Z]{3}-[0-9]{6}'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
