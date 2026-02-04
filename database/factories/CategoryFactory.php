<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'icon' => '🍔',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
