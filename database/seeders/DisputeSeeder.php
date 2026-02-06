<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisputeSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::with('items')->get();
        $adminUsers = User::where('role', 'admin')->get();

        if ($orders->isEmpty()) {
            return;
        }

        $disputeReasons = [
            'Ürün teslim edilmedi',
            'Ürün hasarlı geldi',
            'Yanlış ürün gönderildi',
            'Satıcı ile iletişim kurulamıyor',
            'İade talebi reddedildi',
            'Ürün sahte/taklit',
            'Fatura düzenlenmedi',
        ];

        $resolutionTypes = ['refund', 'replacement', 'partial_refund', 'rejected', 'mediation'];

        // Siparişlerin %10'u için itiraz oluştur
        $disputeOrders = $orders->random(max(1, (int) ($orders->count() * 0.1)));

        foreach ($disputeOrders as $order) {
            $item = $order->items->random();
            $status = fake()->randomElement(['open', 'under_review', 'resolved', 'closed']);

            $assignedTo = $status !== 'open' && $adminUsers->isNotEmpty()
                ? $adminUsers->random()->id
                : null;

            $resolvedBy = in_array($status, ['resolved', 'closed']) && $adminUsers->isNotEmpty()
                ? $adminUsers->random()->id
                : null;

            $assignedAt = $assignedTo ? fake()->dateTimeBetween($order->created_at, 'now') : null;
            $resolvedAt = $resolvedBy ? fake()->dateTimeBetween($assignedAt ?? $order->created_at, 'now') : null;

            DB::table('disputes')->insert([
                'order_item_id' => $item->id,
                'user_id' => $order->user_id,
                'vendor_id' => $item->vendor_id,
                'reason' => fake()->randomElement($disputeReasons),
                'status' => $status,
                'assigned_to' => $assignedTo,
                'assigned_at' => $assignedAt,
                'resolution_notes' => $resolvedBy ? fake()->paragraph() : null,
                'resolved_by' => $resolvedBy,
                'resolved_at' => $resolvedAt,
                'resolution_type' => $resolvedBy ? fake()->randomElement($resolutionTypes) : null,
                'evidence_files' => fake()->boolean(30) ? json_encode([
                    'images' => [
                        'evidence_' . fake()->uuid() . '.jpg',
                        'evidence_' . fake()->uuid() . '.jpg',
                    ],
                    'documents' => [
                        'receipt_' . fake()->uuid() . '.pdf',
                    ],
                ]) : null,
                'created_at' => fake()->dateTimeBetween($order->created_at, 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
