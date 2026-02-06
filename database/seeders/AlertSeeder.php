<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Price alerts
        foreach ($customers as $customer) {
            // Her müşteri 0-3 fiyat alarmı kursun
            $alertCount = rand(0, min(3, $products->count()));
            if ($alertCount > 0) {
                $selectedProducts = $products->random($alertCount);

                foreach ($selectedProducts as $product) {
                    $currentPrice = $product->price ?? 100;
                    $targetPrice = $currentPrice * fake()->randomFloat(2, 0.7, 0.9); // %10-30 düşük fiyat hedefi

                    DB::table('price_alerts')->insert([
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'target_price' => round($targetPrice, 2),
                        'is_active' => fake()->boolean(80),
                        'notified_at' => fake()->boolean(20) ? fake()->dateTimeBetween('-7 days', 'now') : null,
                        'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Stock alerts
        foreach ($customers as $customer) {
            // Her müşteri 0-2 stok alarmı kursun
            $alertCount = rand(0, min(2, $products->count()));
            if ($alertCount > 0) {
                $selectedProducts = $products->random($alertCount);

                foreach ($selectedProducts as $product) {
                    DB::table('stock_alerts')->insert([
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'is_active' => true,
                        'notified_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-7 days', 'now') : null,
                        'created_at' => fake()->dateTimeBetween('-14 days', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
