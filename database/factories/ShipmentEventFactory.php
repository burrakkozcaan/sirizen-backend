<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentEvent>
 */
class ShipmentEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'status' => fake()->randomElement([
                'Sipariş Alındı',
                'Onaylandı',
                'Hazırlanıyor',
                'Kargoya Verildi',
                'Yolda',
                'Teslim Edildi',
            ]),
            'location' => fake()->city(),
            'description' => fake()->sentence(),
            'occurred_at' => now()->subHours(fake()->numberBetween(1, 72)),
            'meta' => [
                'source' => 'carrier',
            ],
        ];
    }
}
