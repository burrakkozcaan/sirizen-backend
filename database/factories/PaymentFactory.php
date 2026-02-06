<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\PaymentProvider;
use App\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'payment_provider' => PaymentProvider::Test,
            'payment_type' => 'card',
            'status' => PaymentStatus::Pending,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Completed,
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Failed,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Refunded,
            'refunded_at' => now(),
        ]);
    }
}
