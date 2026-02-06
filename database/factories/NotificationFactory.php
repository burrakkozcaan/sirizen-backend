<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
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
            'order_id' => null,
            'shipment_id' => Shipment::factory(),
            'type' => 'shipment_status',
            'channel' => fake()->randomElement(['push', 'email', 'sms']),
            'title' => 'Kargo durumu güncellendi',
            'message' => fake()->sentence(),
            'data' => [
                'status' => 'Yolda',
            ],
            'sent_at' => now(),
            'read_at' => null,
        ];
    }
}
