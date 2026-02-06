<?php

namespace Database\Seeders;

use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = ProductReview::all();
        $customers = User::where('role', 'customer')->get();

        if ($reviews->isEmpty() || $customers->isEmpty()) {
            return;
        }

        $imageCreated = false;

        foreach ($reviews as $review) {
            // %40 ihtimalle review'a resim ekle
            if (! $imageCreated || fake()->boolean(40)) {
                $imageCount = rand(1, 3);
                for ($i = 0; $i < $imageCount; $i++) {
                    DB::table('review_images')->insert([
                        'product_review_id' => $review->id,
                        'image_path' => 'reviews/' . fake()->uuid() . '.jpg',
                        'alt_text' => 'Ürün değerlendirme görseli ' . ($i + 1),
                        'sort_order' => $i,
                        'created_at' => $review->created_at,
                        'updated_at' => now(),
                    ]);
                }

                $imageCreated = true;
            }

            // Her review için 0-10 helpful vote ekle
            $voteCount = rand(0, 10);
            if ($voteCount > 0) {
                $voters = $customers->random(min($voteCount, $customers->count()));
                foreach ($voters as $voter) {
                    // Kendi review'ına oy vermesin
                    if ($voter->id !== $review->user_id) {
                        DB::table('review_helpful_votes')->insert([
                            'product_review_id' => $review->id,
                            'user_id' => $voter->id,
                            'is_helpful' => fake()->boolean(75), // %75 faydalı
                            'created_at' => fake()->dateTimeBetween($review->created_at, 'now'),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
