<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSafetyImage>
 */
class ProductSafetyImageFactory extends Factory
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
            'image' => fake()->imageUrl(800, 800, 'product', true),
            'title' => fake()->sentence(3),
            'alt' => fake()->sentence(6),
            'order' => fake()->numberBetween(0, 5),
        ];
    }
}
