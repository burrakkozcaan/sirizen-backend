<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($customers as $customer) {
            // Her kullanıcı 0-8 ürün favorilesin
            $favoriteCount = rand(0, min(8, $products->count()));

            if ($favoriteCount > 0) {
                $selectedProducts = $products->random($favoriteCount);

                foreach ($selectedProducts as $product) {
                    DB::table('favorites')->insert([
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'created_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }
}
