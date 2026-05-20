<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), //Str::random(10),
            'price' => rand(1000, 10000),
            'description' => fake()->text(100), //Str::random(20),
            'status' => ['new', 'used'][rand(0, 1)],
            'is_active' => true,
            'release_date' => now()->subDays(rand(1, 365)),
        ];
    }
}