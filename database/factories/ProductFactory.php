<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);
        $price = fake()->randomFloat(2, 50, 3000);
        $discountPrice = fake()->optional(0.4)->randomFloat(2, 30, $price);

        return [
            'brand_id' => null,
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'additional_information' => fake()->paragraph(),
            'safety_information' => fake()->paragraph(),
            'manufacturer_name' => fake()->company(),
            'manufacturer_address' => fake()->address(),
            'manufacturer_contact' => fake()->phoneNumber(),
            'responsible_party_name' => fake()->company(),
            'responsible_party_address' => fake()->address(),
            'responsible_party_contact' => fake()->phoneNumber(),
            'rating' => fake()->randomFloat(2, 0, 5),
            'reviews_count' => fake()->numberBetween(0, 200),
            'is_active' => true,
            'price' => $price,
            'discount_price' => $discountPrice,
        ];
    }
}
