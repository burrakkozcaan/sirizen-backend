<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasVendorResponse = fake()->boolean(50);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->sentence(),
            'vendor_response' => $hasVendorResponse ? fake()->sentence() : null,
            'vendor_response_at' => $hasVendorResponse ? now()->subDays(fake()->numberBetween(0, 10)) : null,
            'is_verified_purchase' => fake()->boolean(70),
        ];
    }
}
