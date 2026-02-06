<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecentlyViewedSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($customers as $customer) {
            // Her kullanıcı 1-products.count ürün görüntülemiş olsun
            $maxViews = min(15, $products->count());
            $viewCount = rand(1, $maxViews);
            $viewedProducts = $products->random($viewCount);

            foreach ($viewedProducts as $product) {
                DB::table('recently_vieweds')->insert([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'viewed_at' => fake()->dateTimeBetween('-7 days', 'now'),
                ]);
            }
        }
    }
}
