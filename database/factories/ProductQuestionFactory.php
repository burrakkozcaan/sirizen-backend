<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductQuestion>
 */
class ProductQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $answeredByVendor = fake()->boolean(60);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'question' => fake()->sentence(),
            'answer' => $answeredByVendor ? fake()->sentence() : null,
            'answered_by_vendor' => $answeredByVendor,
        ];
    }
}
