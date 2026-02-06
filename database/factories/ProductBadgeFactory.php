<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductBadge>
 */
class ProductBadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'label' => fake()->words(2, true),
            'color' => fake()->randomElement(['danger', 'warning', 'success', 'info', 'gray']),
            'icon' => fake()->optional()->word(),
        ];
    }
}
