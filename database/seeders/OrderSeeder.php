<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSeller;
use App\Models\User;
use App\OrderItemStatus;
use App\OrderStatus;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $productSellers = ProductSeller::with(['product', 'variant', 'vendor'])
            ->where('stock', '>', 0)
            ->get();

        if ($users->isEmpty() || $productSellers->isEmpty()) {
            return;
        }

        // Her kullanıcı için 1-5 sipariş oluştur
        foreach ($users as $user) {
            $orderCount = fake()->numberBetween(1, 5);
            $userAddress = Address::where('user_id', $user->id)->first();

            for ($i = 0; $i < $orderCount; $i++) {
                $order = $this->createOrder($user, $userAddress);
                $this->createOrderItems($order, $productSellers);
                $this->updateOrderTotal($order);
            }
        }
    }

    private function createOrder(User $user, ?Address $address): Order
    {
        $status = fake()->randomElement([
            OrderStatus::PENDING->value,
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
            OrderStatus::CANCELLED->value,
        ]);

        $createdAt = fake()->dateTimeBetween('-3 months', 'now');

        return Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-'.strtoupper(fake()->unique()->bothify('##??####')),
            'total_price' => 0,
            'status' => $status,
            'payment_method' => fake()->randomElement(['credit_card', 'bank_transfer', 'cash_on_delivery']),
            'address_id' => $address?->id,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function createOrderItems(Order $order, $productSellers): void
    {
        $itemCount = fake()->numberBetween(1, 4);
        $selectedSellers = $productSellers->random(min($itemCount, $productSellers->count()));

        foreach ($selectedSellers as $productSeller) {
            $quantity = fake()->numberBetween(1, 3);
            $unitPrice = $productSeller->sale_price ?? $productSeller->price;
            $totalPrice = $unitPrice * $quantity;

            // Sipariş durumuna göre item durumu
            $itemStatus = $this->getItemStatusFromOrderStatus($order->status);

            // Variant snapshot oluştur
            $variantSnapshot = null;
            if ($productSeller->variant) {
                $variantSnapshot = [
                    'id' => $productSeller->variant->id,
                    'sku' => $productSeller->variant->sku,
                    'barcode' => $productSeller->variant->barcode,
                    'color' => $productSeller->variant->color,
                    'size' => $productSeller->variant->size,
                    'price' => $productSeller->variant->price,
                ];
            }

            OrderItem::create([
                'order_id' => $order->id,
                'vendor_id' => $productSeller->vendor_id,
                'product_seller_id' => $productSeller->id,
                'product_id' => $productSeller->product_id,
                'variant_id' => $productSeller->variant_id,
                'variant_snapshot' => $variantSnapshot,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'price' => $totalPrice,
                'status' => $itemStatus,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
        }
    }

    private function getItemStatusFromOrderStatus(string $orderStatus): string
    {
        return match ($orderStatus) {
            OrderStatus::PENDING->value => OrderItemStatus::PENDING->value,
            OrderStatus::CONFIRMED->value => OrderItemStatus::PREPARING->value,
            OrderStatus::PROCESSING->value => OrderItemStatus::PREPARING->value,
            OrderStatus::SHIPPED->value => OrderItemStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value => OrderItemStatus::DELIVERED->value,
            OrderStatus::CANCELLED->value => OrderItemStatus::CANCELLED->value,
            OrderStatus::REFUNDED->value => OrderItemStatus::RETURNED->value,
            default => OrderItemStatus::PENDING->value,
        };
    }

    private function updateOrderTotal(Order $order): void
    {
        $total = $order->items()->sum('price');
        $order->update(['total_price' => $total]);
    }
}
