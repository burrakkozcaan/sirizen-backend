<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\ProductSeller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $productSellers = ProductSeller::where('stock', '>', 0)->get();

        if ($customers->isEmpty() || $productSellers->isEmpty()) {
            return;
        }

        foreach ($customers as $customer) {
            // %60 ihtimalle sepeti olsun
            if (fake()->boolean(60)) {
                $cart = Cart::create([
                    'user_id' => $customer->id,
                ]);

                // 1-5 ürün ekle
                $itemCount = rand(1, 5);
                $selectedSellers = $productSellers->random(min($itemCount, $productSellers->count()));

                foreach ($selectedSellers as $seller) {
                    $quantity = rand(1, 3);
                    $price = $seller->sale_price ?? $seller->price;

                    DB::table('cart_items')->insert([
                        'cart_id' => $cart->id,
                        'product_id' => $seller->product_id,
                        'product_seller_id' => $seller->id,
                        'quantity' => $quantity,
                        'price' => $price * $quantity,
                        'created_at' => now()->subHours(rand(1, 72)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
