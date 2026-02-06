<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVideo>
 */
class ProductVideoFactory extends Factory
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
            'url' => 'https://www.youtube.com/watch?v=' . fake()->regexify('[A-Za-z0-9_-]{11}'),
            'video_type' => fake()->randomElement(['youtube', 'vimeo']),
            'order' => fake()->numberBetween(0, 5),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
