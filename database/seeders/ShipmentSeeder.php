<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ShippingCompany;
use App\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $shippedOrders = Order::whereIn('status', [
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ])->with(['items', 'address'])->get();

        $shippingCompanies = ShippingCompany::all();

        if ($shippedOrders->isEmpty() || $shippingCompanies->isEmpty()) {
            return;
        }

        foreach ($shippedOrders as $order) {
            $company = $shippingCompanies->random();
            $isDelivered = $order->status === OrderStatus::DELIVERED->value;

            $shippedAt = fake()->dateTimeBetween($order->created_at, 'now');
            $estimatedDelivery = (clone $shippedAt)->modify('+' . rand(1, 5) . ' days');
            $deliveredAt = $isDelivered ? fake()->dateTimeBetween($shippedAt, 'now') : null;

            $shipmentId = DB::table('shipments')->insertGetId([
                'order_id' => $order->id,
                'order_item_id' => $order->items->first()?->id,
                'vendor_id' => $order->items->first()?->vendor_id,
                'address_id' => $order->address_id,
                'shipping_company_id' => $company->id,
                'tracking_number' => strtoupper($company->code ?? 'TRK') . fake()->numerify('##########'),
                'carrier' => $company->name,
                'tracking_url' => $company->tracking_url ? str_replace('{tracking_number}', '', $company->tracking_url) : null,
                'status' => $isDelivered ? 'delivered' : fake()->randomElement(['in_transit', 'out_for_delivery']),
                'current_location' => fake()->city() . ', Türkiye',
                'current_latitude' => fake()->latitude(36, 42),
                'current_longitude' => fake()->longitude(26, 45),
                'progress_percent' => $isDelivered ? 100 : rand(30, 90),
                'estimated_delivery' => $estimatedDelivery,
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
                'notify_on_status_change' => fake()->boolean(80),
                'created_at' => $shippedAt,
                'updated_at' => now(),
            ]);

            // Shipment events
            $this->createShipmentEvents($shipmentId, $shippedAt, $deliveredAt, $isDelivered);
        }
    }

    private function createShipmentEvents(int $shipmentId, $shippedAt, $deliveredAt, bool $isDelivered): void
    {
        $events = [
            [
                'status' => 'picked_up',
                'location' => fake()->city() . ' Depo',
                'description' => 'Kargo teslim alındı',
                'occurred_at' => $shippedAt,
            ],
            [
                'status' => 'in_transit',
                'location' => fake()->city() . ' Transfer Merkezi',
                'description' => 'Kargo yolda',
                'occurred_at' => (clone $shippedAt)->modify('+' . rand(4, 12) . ' hours'),
            ],
        ];

        if ($isDelivered) {
            $events[] = [
                'status' => 'out_for_delivery',
                'location' => fake()->city() . ' Dağıtım Merkezi',
                'description' => 'Dağıtıma çıktı',
                'occurred_at' => (clone $deliveredAt)->modify('-' . rand(2, 6) . ' hours'),
            ];
            $events[] = [
                'status' => 'delivered',
                'location' => fake()->address(),
                'description' => 'Teslim edildi',
                'occurred_at' => $deliveredAt,
            ];
        } else {
            $events[] = [
                'status' => 'in_transit',
                'location' => fake()->city() . ' Aktarma Merkezi',
                'description' => 'Aktarma merkezinde',
                'occurred_at' => now()->subHours(rand(1, 24)),
            ];
        }

        foreach ($events as $event) {
            DB::table('shipment_events')->insert([
                'shipment_id' => $shipmentId,
                'status' => $event['status'],
                'location' => $event['location'],
                'description' => $event['description'],
                'occurred_at' => $event['occurred_at'],
                'meta' => json_encode(['source' => 'api', 'raw_status' => strtoupper($event['status'])]),
                'created_at' => $event['occurred_at'],
                'updated_at' => now(),
            ]);
        }
    }
}
