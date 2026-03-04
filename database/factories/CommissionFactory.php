<?php

namespace Database\Factories;

use App\CommissionStatus;
use App\Models\OrderItem;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    public function definition(): array
    {
        $gross            = fake()->randomFloat(2, 50, 500);
        $commissionRate   = fake()->randomFloat(2, 8, 22);
        $vatRate          = 0.20;
        $amountExclVat    = $gross / (1 + $vatRate);
        $commissionAmount = round($amountExclVat * ($commissionRate / 100), 2);
        $netAmount        = round($gross - $commissionAmount, 2);

        return [
            'vendor_id'         => Vendor::factory(),
            'order_item_id'     => OrderItem::factory(),
            'payment_id'        => null,
            'gross_amount'      => $gross,
            'commission_rate'   => $commissionRate,
            'commission_amount' => $commissionAmount,
            'net_amount'        => $netAmount,
            'currency'          => 'TRY',
            'refunded_amount'   => 0,
            'status'            => CommissionStatus::PENDING,
            'settled_at'        => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => CommissionStatus::PAID]);
    }

    public function settled(): static
    {
        return $this->state(fn () => [
            'status'     => CommissionStatus::SETTLED,
            'settled_at' => now(),
        ]);
    }
}
