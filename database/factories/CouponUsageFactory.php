<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponUsage>
 */
class CouponUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coupon_id' => Coupon::factory(),
            'user_id' => User::factory(),
            'order_id' => fake()->boolean(60) ? Order::factory() : null,
            'discount_amount' => fake()->randomFloat(2, 5, 100),
            'used_at' => now()->subDays(fake()->numberBetween(0, 10)),
        ];
    }
}
