<?php

namespace Database\Factories;

use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewImage>
 */
class ReviewImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_review_id' => ProductReview::factory(),
            'image_path' => 'reviews/images/'.fake()->uuid().'.jpg',
            'alt_text' => fake()->sentence(3),
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }
}
