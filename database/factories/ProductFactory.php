<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'code' => 'prd-' . Str::random(5),
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'base_price' => $this->faker->numberBetween(10000, 50000),
            'average_rating' => $this->faker->randomFloat(2, 4, 5),
            'is_available' => true,
        ];
    }
}
