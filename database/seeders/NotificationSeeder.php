<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $orders = Order::with('items')->get();

        if ($users->isEmpty()) {
            return;
        }

        $notificationTypes = [
            'order_placed' => [
                'title' => 'Siparişiniz Alındı',
                'message' => 'Siparişiniz başarıyla oluşturuldu. Sipariş numaranız: {order_number}',
            ],
            'order_confirmed' => [
                'title' => 'Siparişiniz Onaylandı',
                'message' => 'Siparişiniz satıcı tarafından onaylandı ve hazırlanıyor.',
            ],
            'order_shipped' => [
                'title' => 'Siparişiniz Kargoya Verildi',
                'message' => 'Siparişiniz kargoya verildi. Takip numarası: {tracking_number}',
            ],
            'order_delivered' => [
                'title' => 'Siparişiniz Teslim Edildi',
                'message' => 'Siparişiniz teslim edildi. İyi alışverişler!',
            ],
            'price_drop' => [
                'title' => 'Fiyat Düştü!',
                'message' => 'Takip ettiğiniz ürünün fiyatı düştü.',
            ],
            'back_in_stock' => [
                'title' => 'Ürün Stoklara Girdi',
                'message' => 'Beklediğiniz ürün stoklara girdi.',
            ],
            'campaign' => [
                'title' => 'Yeni Kampanya!',
                'message' => 'Kaçırmayın! Özel indirim fırsatları sizi bekliyor.',
            ],
            'review_reminder' => [
                'title' => 'Ürünü Değerlendirin',
                'message' => 'Aldığınız ürünü değerlendirerek diğer kullanıcılara yardımcı olun.',
            ],
        ];

        foreach ($users as $user) {
            // Her kullanıcı için 3-10 bildirim
            $notificationCount = rand(3, 10);
            $userOrders = $orders->where('user_id', $user->id);

            for ($i = 0; $i < $notificationCount; $i++) {
                $type = array_rand($notificationTypes);
                $template = $notificationTypes[$type];

                $order = $userOrders->isNotEmpty() ? $userOrders->random() : null;
                $shipment = null;

                $message = str_replace(
                    ['{order_number}', '{tracking_number}'],
                    [$order?->order_number ?? 'ORD-XXXXX', 'TRK' . fake()->numerify('##########')],
                    $template['message']
                );

                $sentAt = fake()->dateTimeBetween('-30 days', 'now');
                $readAt = fake()->boolean(60) ? fake()->dateTimeBetween($sentAt, 'now') : null;

                DB::table('notifications')->insert([
                    'user_id' => $user->id,
                    'order_id' => in_array($type, ['order_placed', 'order_confirmed', 'order_shipped', 'order_delivered']) ? $order?->id : null,
                    'shipment_id' => $shipment,
                    'type' => $type,
                    'channel' => fake()->randomElement(['push', 'email', 'sms', 'in_app']),
                    'title' => $template['title'],
                    'message' => $message,
                    'data' => json_encode([
                        'order_id' => $order?->id,
                        'action_url' => $order ? '/orders/' . $order->id : null,
                    ]),
                    'sent_at' => $sentAt,
                    'read_at' => $readAt,
                    'created_at' => $sentAt,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
