<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        if ($orders->isEmpty()) {
            return;
        }

        $gateways = ['iyzico', 'paytr', 'test'];
        $methods = ['credit_card', 'debit_card', 'bank_transfer', 'cash_on_delivery'];

        foreach ($orders as $order) {
            $amount = $order->total_price;
            $commissionRate = fake()->randomFloat(2, 0.05, 0.15); // %5-15 komisyon
            $commissionAmount = $amount * $commissionRate;
            $vendorAmount = $amount - $commissionAmount;
            $platformAmount = $commissionAmount * 0.3; // Platform payı

            $status = fake()->randomElement(['pending', 'completed', 'failed', 'refunded']);
            $gateway = fake()->randomElement($gateways);
            $method = fake()->randomElement($methods);

            DB::table('payments')->insert([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'amount' => $amount,
                'payment_provider' => $gateway,
                'payment_type' => $method,
                'status' => $status,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
                'paid_at' => $status === 'completed' ? fake()->dateTimeBetween($order->created_at, 'now') : null,
                'commission_amount' => $commissionAmount,
                'vendor_amount' => $vendorAmount,
                'platform_amount' => $platformAmount,
                'split_status' => match ($status) {
                    'completed' => fake()->randomElement(['split', 'settled']),
                    'pending' => 'pending',
                    'failed' => 'pending',
                    'refunded' => 'refunded',
                    default => 'pending',
                },
                'gateway' => $gateway,
                'method' => $method,
                'gateway_response' => json_encode([
                    'status' => $status,
                    'transaction_id' => 'GW-' . Str::random(20),
                    'auth_code' => fake()->numerify('######'),
                ]),
                'error_message' => $status === 'failed' ? 'Ödeme başarısız: Yetersiz bakiye' : null,
                'installment' => $method === 'credit_card' ? fake()->randomElement([1, 3, 6, 9, 12]) : 1,
                'created_at' => $order->created_at,
                'updated_at' => now(),
            ]);
        }
    }
}
