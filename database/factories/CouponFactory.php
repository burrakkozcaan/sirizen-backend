<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => fake()->boolean(70) ? Vendor::factory() : null,
            'product_id' => fake()->boolean(40) ? Product::factory() : null,
            'code' => strtoupper(fake()->bothify('KUPON-####')),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->optional()->randomFloat(2, 50, 500),
            'max_discount_amount' => fake()->optional()->randomFloat(2, 50, 250),
            'usage_limit' => fake()->optional()->numberBetween(10, 500),
            'per_user_limit' => fake()->optional()->numberBetween(1, 5),
            'starts_at' => now()->subDays(fake()->numberBetween(1, 5)),
            'expires_at' => now()->addDays(fake()->numberBetween(5, 30)),
            'is_active' => true,
        ];
    }
}
