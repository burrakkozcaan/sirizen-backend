<?php

namespace Database\Seeders;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Onaylanmış ve teslim edilmiş siparişler için fatura
        $orders = Order::whereIn('status', [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ])->with('items')->get();

        if ($orders->isEmpty()) {
            return;
        }

        $invoiceNumber = 1000;

        foreach ($orders as $order) {
            $vendorIds = $order->items->pluck('vendor_id')->unique()->filter();

            foreach ($vendorIds as $vendorId) {
                $vendorItems = $order->items->where('vendor_id', $vendorId);
                $subtotal = $vendorItems->sum('price');
                $taxAmount = $subtotal * 0.20; // KDV %20
                $totalAmount = $subtotal + $taxAmount;

                $invoiceNumber++;
                $status = fake()->randomElement(['draft', 'sent', 'delivered', 'cancelled']);

                DB::table('invoices')->insert([
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'user_id' => $order->user_id,
                    'invoice_number' => 'FTR-' . date('Y') . '-' . str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT),
                    'invoice_type' => 'SATIS',
                    'invoice_scenario' => fake()->randomElement(['TEMEL', 'TICARI', 'IHRACAT']),
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'currency' => 'TRY',
                    'status' => $status,
                    'uuid' => Str::uuid(),
                    'ettn' => strtoupper(Str::random(36)),
                    'invoice_data' => json_encode([
                        'items' => $vendorItems->map(fn ($item) => [
                            'name' => 'Ürün #' . $item->product_id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total' => $item->price,
                        ])->values()->toArray(),
                    ]),
                    'receiver_info' => json_encode([
                        'name' => 'Müşteri',
                        'address' => 'İstanbul, Türkiye',
                        'tax_number' => fake()->numerify('###########'),
                    ]),
                    'error_message' => $status === 'cancelled' ? 'İptal edildi' : null,
                    'sent_at' => in_array($status, ['sent', 'delivered']) ? $order->created_at : null,
                    'delivered_at' => $status === 'delivered' ? fake()->dateTimeBetween($order->created_at, 'now') : null,
                    'cancelled_at' => $status === 'cancelled' ? fake()->dateTimeBetween($order->created_at, 'now') : null,
                    'created_at' => $order->created_at,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
