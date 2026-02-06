<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $wishlistNames = [
            'Almak İstediklerim',
            'Doğum Günü Listesi',
            'Yaz Alışverişi',
            'Ev Dekorasyonu',
            'Hediye Fikirleri',
            'İndirim Beklediklerim',
        ];

        foreach ($customers as $customer) {
            // Her kullanıcı 1-3 liste oluştursun
            $listCount = rand(1, 3);

            for ($i = 0; $i < $listCount; $i++) {
                $wishlist = Wishlist::create([
                    'user_id' => $customer->id,
                    'name' => fake()->randomElement($wishlistNames) . ' ' . ($i + 1),
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);

                // Her listeye 1-5 ürün ekle
                $itemCount = rand(1, min(5, $products->count()));
                $selectedProducts = $products->random($itemCount);

                foreach ($selectedProducts as $product) {
                    DB::table('wishlist_items')->insert([
                        'wishlist_id' => $wishlist->id,
                        'product_id' => $product->id,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
