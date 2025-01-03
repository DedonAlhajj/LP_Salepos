<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFactory extends Factory
{
    public function definition()
    {
        return [
            'description' => $this->faker->sentence,
        ];
    }
}
