<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSafetyDocument>
 */
class ProductSafetyDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'title' => fake()->sentence(3),
            'file' => 'https://example.com/documents/'.fake()->uuid().'.pdf',
            'order' => fake()->numberBetween(0, 5),
        ];
    }
}
