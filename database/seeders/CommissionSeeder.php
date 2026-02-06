<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Vendor;
use App\OrderItemStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        $orderItems = OrderItem::whereNotNull('vendor_id')->with('vendor')->get();

        if ($orderItems->isEmpty()) {
            return;
        }

        foreach ($orderItems as $item) {
            $commissionRate = fake()->randomFloat(2, 0.08, 0.18); // %8-18 komisyon
            $commissionAmount = $item->price * $commissionRate;
            $netAmount = $item->price - $commissionAmount;

            $status = match ($item->status) {
                OrderItemStatus::DELIVERED->value => fake()->randomElement(['calculated', 'paid']),
                OrderItemStatus::CANCELLED->value, OrderItemStatus::RETURNED->value => 'cancelled',
                default => 'pending',
            };

            DB::table('commissions')->insert([
                'vendor_id' => $item->vendor_id,
                'order_item_id' => $item->id,
                'commission_rate' => $commissionRate * 100, // Yüzde olarak
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'status' => $status,
                'created_at' => $item->created_at,
                'updated_at' => now(),
            ]);
        }
    }
}
