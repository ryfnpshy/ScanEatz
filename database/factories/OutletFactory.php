<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OutletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'otl-' . Str::random(5),
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'district' => $this->faker->citySuffix,
            'city' => 'Jakarta Pusat',
            'latitude' => -6.175,
            'longitude' => 106.827,
            'phone' => $this->faker->phoneNumber,
            'is_active' => true,
        ];
    }
}
