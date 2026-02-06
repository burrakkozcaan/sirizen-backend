<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
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
            'title' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'banner' => fake()->imageUrl(1200, 320, 'fashion'),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 60),
            'starts_at' => now()->subDays(fake()->numberBetween(1, 10)),
            'ends_at' => now()->addDays(fake()->numberBetween(5, 30)),
            'is_active' => true,
        ];
    }
}
