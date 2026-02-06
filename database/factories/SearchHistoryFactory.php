<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SearchHistory>
 */
class SearchHistoryFactory extends Factory
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
            'query' => fake()->words(3, true),
            'results_count' => fake()->numberBetween(0, 250),
            'searched_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
