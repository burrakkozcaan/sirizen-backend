<?php

namespace Database\Seeders;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Teslim edilmiş siparişler için satıcı değerlendirmesi
        $deliveredOrders = Order::where('status', OrderStatus::DELIVERED->value)
            ->with('items')
            ->get();

        if ($deliveredOrders->isEmpty()) {
            return;
        }

        $comments = [
            'positive' => [
                'Çok hızlı kargo, ürün sorunsuz geldi. Teşekkürler!',
                'İletişim çok iyi, sorularıma hemen cevap verdiler.',
                'Paketleme mükemmeldi, ürün hasarsız geldi.',
                'Fiyat/performans açısından çok memnunum.',
                'Güvenilir satıcı, tekrar alışveriş yaparım.',
            ],
            'neutral' => [
                'Ürün iyi ama kargo biraz geç geldi.',
                'Fiyat biraz yüksek ama kalite iyi.',
                'Normal bir alışveriş deneyimiydi.',
            ],
            'negative' => [
                'Kargo çok geç geldi, iletişim zayıf.',
                'Paketleme yetersizdi, ürün hafif hasarlı geldi.',
                'Beklentilerimi karşılamadı.',
            ],
        ];

        foreach ($deliveredOrders as $order) {
            // %60 ihtimalle satıcı değerlendirmesi yapsın
            if (fake()->boolean(60)) {
                $vendorIds = $order->items->pluck('vendor_id')->unique()->filter();

                foreach ($vendorIds as $vendorId) {
                    if (! $vendorId) {
                        continue;
                    }

                    $deliveryRating = fake()->numberBetween(3, 5);
                    $communicationRating = fake()->numberBetween(3, 5);
                    $packagingRating = fake()->numberBetween(3, 5);

                    $avgRating = ($deliveryRating + $communicationRating + $packagingRating) / 3;
                    $commentType = match (true) {
                        $avgRating >= 4 => 'positive',
                        $avgRating >= 3 => 'neutral',
                        default => 'negative',
                    };

                    DB::table('seller_reviews')->insert([
                        'user_id' => $order->user_id,
                        'vendor_id' => $vendorId,
                        'delivery_rating' => $deliveryRating,
                        'communication_rating' => $communicationRating,
                        'packaging_rating' => $packagingRating,
                        'comment' => fake()->randomElement($comments[$commentType]),
                        'created_at' => fake()->dateTimeBetween($order->updated_at, 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
