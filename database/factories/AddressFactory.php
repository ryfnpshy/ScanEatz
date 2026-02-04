<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Rumah',
            'full_address' => $this->faker->address,
            'district' => 'Gambir',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'latitude' => -6.175,
            'longitude' => 106.827,
            'is_default' => true,
        ];
    }
}
