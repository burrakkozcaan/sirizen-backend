<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceAlert>
 */
class PriceAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'target_price' => fake()->randomFloat(2, 10, 5000),
            'is_active' => fake()->boolean(85),
            'notified_at' => null,
        ];
    }
}
