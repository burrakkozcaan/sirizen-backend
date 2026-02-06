<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorPenalty>
 */
class VendorPenaltyFactory extends Factory
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
            'reason' => fake()->sentence(),
            'penalty_points' => fake()->numberBetween(1, 50),
            'expires_at' => fake()->optional()->dateTimeBetween('+1 days', '+2 months'),
        ];
    }
}
