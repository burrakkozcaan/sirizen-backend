<?php

namespace Database\Seeders;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefundSeeder extends Seeder
{
    public function run(): void
    {
        // İptal veya iade edilmiş siparişler
        $cancelledOrders = Order::whereIn('status', [
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUNDED->value,
        ])->with('items')->get();

        $refundReasons = [
            'Ürün hasarlı geldi',
            'Yanlış ürün gönderildi',
            'Ürün açıklamaya uymuyor',
            'Beden/boyut uygun değil',
            'Müşteri vazgeçti',
            'Ürün kalitesi beklentileri karşılamadı',
        ];

        foreach ($cancelledOrders as $order) {
            foreach ($order->items as $item) {
                // Her item için iade kaydı
                DB::table('refunds')->insert([
                    'order_item_id' => $item->id,
                    'user_id' => $order->user_id,
                    'vendor_id' => $item->vendor_id,
                    'reason' => fake()->randomElement($refundReasons),
                    'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'completed']),
                    'refund_amount' => $item->price,
                    'created_at' => $order->updated_at,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
