<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', UserRole::CUSTOMER)->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $reviews = [
            ['rating' => 5, 'comment' => 'Harika bir ürün! Tam beklediğim gibi, kalitesi çok iyi. Herkese tavsiye ederim.'],
            ['rating' => 5, 'comment' => 'Çok memnun kaldım. Ürün açıklamasındaki gibi geldi, kalitesi mükemmel.'],
            ['rating' => 4, 'comment' => 'Gayet güzel bir ürün. Fiyat/performans açısından çok iyi. Tavsiye ederim.'],
            ['rating' => 4, 'comment' => 'Ürün kaliteli ama kargo biraz geç geldi. Ama ürüne kesinlikle değer.'],
            ['rating' => 5, 'comment' => 'Mükemmel! Tam istediğim gibi. Rengi ve kalitesi harika.'],
            ['rating' => 3, 'comment' => 'Fena değil ama fotoğraflardaki kadar güzel değilmiş. Yine de idare eder.'],
            ['rating' => 5, 'comment' => 'Çok kaliteli bir ürün. Fiyatına göre harika. Teşekkürler.'],
            ['rating' => 4, 'comment' => 'Güzel ürün, beğendim. Bedeni tam oldu. Kargo hızlıydı.'],
        ];

        foreach ($products as $product) {
            $reviewCount = rand(2, 5);

            for ($i = 0; $i < $reviewCount; $i++) {
                if (! isset($customers[$i])) {
                    break;
                }

                $reviewData = $reviews[array_rand($reviews)];

                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $customers[$i]->id,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_verified_purchase' => rand(0, 1) == 1,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
