<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(4, true),
            'cover_image' => null,
            'is_published' => fake()->boolean(60),
            'published_at' => null,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }
}
