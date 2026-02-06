<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorScore>
 */
class VendorScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'total_score' => fake()->randomFloat(2, 0, 100),
            'delivery_score' => fake()->randomFloat(2, 0, 100),
            'rating_score' => fake()->randomFloat(2, 0, 100),
            'stock_score' => fake()->randomFloat(2, 0, 100),
            'support_score' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
