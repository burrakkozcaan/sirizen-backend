<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductBanner>
 */
class ProductBannerFactory extends Factory
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
            'title' => fake()->sentence(3),
            'image' => fake()->imageUrl(1200, 320, 'fashion'),
            'position' => fake()->randomElement(['top_left', 'top_right', 'under_gallery']),
            'is_active' => fake()->boolean(85),
        ];
    }
}
