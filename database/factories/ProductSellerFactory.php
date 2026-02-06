<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSeller>
 */
class ProductSellerFactory extends Factory
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
            'vendor_id' => Vendor::factory(),
            'price' => fake()->randomFloat(2, 50, 2000),
            'stock' => fake()->numberBetween(0, 200),
            'dispatch_days' => fake()->numberBetween(0, 5),
            'shipping_type' => fake()->randomElement(['normal', 'express', 'same_day']),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
