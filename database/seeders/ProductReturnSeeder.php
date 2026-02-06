<?php

namespace Database\Seeders;

use App\ReturnReason;
use App\Models\Order;
use App\Models\ShippingCompany;
use App\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReturnSeeder extends Seeder
{
    public function run(): void
    {
        // Teslim edilmiş siparişlerden bazıları iade edilsin
        $deliveredOrders = Order::where('status', OrderStatus::DELIVERED->value)
            ->with('items')
            ->get();

        $shippingCompanies = ShippingCompany::all();

        if ($deliveredOrders->isEmpty()) {
            return;
        }

        $returnReasons = [
            ReturnReason::DEFECTIVE->value => 'Ürün hasarlı/arızalı',
            ReturnReason::WRONG_PRODUCT->value => 'Yanlış ürün gönderildi',
            ReturnReason::NOT_AS_DESCRIBED->value => 'Açıklamaya uymuyor',
            ReturnReason::WRONG_SIZE->value => 'Beden/boyut uygun değil',
            ReturnReason::CHANGED_MIND->value => 'Fikir değişikliği',
            ReturnReason::QUALITY_ISSUE->value => 'Kalite sorunu',
        ];

        // Teslim edilmiş siparişlerin %20'si iade edilsin
        $ordersToReturn = $deliveredOrders->random(max(1, (int) ($deliveredOrders->count() * 0.2)));

        foreach ($ordersToReturn as $order) {
            // Siparişten rastgele 1 item iade et
            $item = $order->items->random();
            $reasonKey = array_rand($returnReasons);

            $requestedAt = fake()->dateTimeBetween($order->updated_at, 'now');
            $status = fake()->randomElement(['pending', 'approved', 'shipped', 'received', 'refunded', 'rejected']);

            $approvedAt = in_array($status, ['approved', 'shipped', 'received', 'refunded'])
                ? fake()->dateTimeBetween($requestedAt, 'now')
                : null;

            $rejectedAt = $status === 'rejected'
                ? fake()->dateTimeBetween($requestedAt, 'now')
                : null;

            DB::table('product_returns')->insert([
                'order_item_id' => $item->id,
                'user_id' => $order->user_id,
                'vendor_id' => $item->vendor_id,
                'reason' => $reasonKey,
                'reason_description' => $returnReasons[$reasonKey] . ' - ' . fake()->sentence(),
                'status' => $status,
                'refund_amount' => $item->price,
                'tracking_number' => in_array($status, ['shipped', 'received', 'refunded'])
                    ? 'RET' . fake()->numerify('##########')
                    : null,
                'carrier' => in_array($status, ['shipped', 'received', 'refunded']) && $shippingCompanies->isNotEmpty()
                    ? $shippingCompanies->random()->name
                    : null,
                'condition_status' => in_array($status, ['received', 'refunded'])
                    ? fake()->randomElement(['good', 'damaged', 'opened'])
                    : null,
                'requested_at' => $requestedAt,
                'approved_at' => $approvedAt,
                'rejected_at' => $rejectedAt,
                'received_at' => in_array($status, ['received', 'refunded'])
                    ? fake()->dateTimeBetween($approvedAt ?? $requestedAt, 'now')
                    : null,
                'created_at' => $requestedAt,
                'updated_at' => now(),
            ]);
        }
    }
}
