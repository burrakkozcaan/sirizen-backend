<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorTier>
 */
class VendorTierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake()->words(2, true),
            'min_total_orders'  => 0,
            'min_rating'        => 0,
            'max_cancel_rate'   => 100,
            'max_return_rate'   => 100,
            'priority_boost'    => 0,
            'badge_icon'        => null,
            'commission_rate'   => null, // null = indirim yok
        ];
    }
}
