<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $searchQueries = [
            'tişört', 'elbise', 'ayakkabı', 'çanta', 'mont', 'pantolon',
            'Nike', 'Adidas', 'Zara', 'Koton', 'jean', 'spor ayakkabı',
            'kış montu', 'yazlık elbise', 'erkek gömlek', 'kadın bluz',
        ];

        $actions = [
            'login', 'logout', 'view_product', 'add_to_cart', 'remove_from_cart',
            'checkout', 'update_profile', 'change_password', 'add_address',
            'submit_review', 'ask_question', 'follow_vendor', 'add_to_wishlist',
        ];

        // Search Logs
        foreach ($users->where('role', 'customer') as $user) {
            $searchCount = fake()->numberBetween(5, 20);

            for ($i = 0; $i < $searchCount; $i++) {
                $query = fake()->randomElement($searchQueries);

                DB::table('search_logs')->insert([
                    'user_id' => $user->id,
                    'query' => $query,
                    'results_count' => fake()->numberBetween(0, 500),
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ]);

                // Search Histories
                DB::table('search_histories')->insert([
                    'user_id' => $user->id,
                    'query' => $query,
                    'results_count' => fake()->numberBetween(0, 500),
                    'searched_at' => fake()->dateTimeBetween('-30 days', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Activity Logs
        foreach ($users as $user) {
            $activityCount = fake()->numberBetween(10, 50);

            for ($i = 0; $i < $activityCount; $i++) {
                $action = fake()->randomElement($actions);

                DB::table('activity_logs')->insert([
                    'user_id' => $user->id,
                    'action' => $action,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'properties' => json_encode([
                        'action' => $action,
                        'timestamp' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                        'details' => fake()->sentence(),
                    ]),
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ]);
            }
        }
    }
}
