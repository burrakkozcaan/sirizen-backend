<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
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
            'vendor_id' => Vendor::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 4),
            'price' => fake()->randomFloat(2, 50, 1500),
            'status' => 'pending',
        ];
    }
}
