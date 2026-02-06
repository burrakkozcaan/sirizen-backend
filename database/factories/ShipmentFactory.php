<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'tracking_number' => 'TR'.Str::upper(fake()->unique()->bothify('#########')),
            'carrier' => fake()->randomElement(['Yurtiçi Kargo', 'Aras Kargo', 'PTT Kargo']),
            'tracking_url' => fake()->url(),
            'current_location' => fake()->city(),
            'progress_percent' => fake()->numberBetween(0, 100),
            'notify_on_status_change' => true,
            'estimated_delivery' => now()->addDays(fake()->numberBetween(1, 7)),
            'shipped_at' => now()->subDays(fake()->numberBetween(0, 2)),
            'delivered_at' => null,
        ];
    }
}
