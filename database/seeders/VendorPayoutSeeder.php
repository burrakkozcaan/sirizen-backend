<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorPayoutSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::where('status', 'active')->get();

        if ($vendors->isEmpty()) {
            return;
        }

        $payoutMethods = ['bank_transfer', 'eft', 'havale'];

        foreach ($vendors as $vendor) {
            // Son 3 ay için haftalık ödeme kayıtları
            for ($week = 12; $week >= 1; $week--) {
                $periodEnd = now()->subWeeks($week - 1)->endOfWeek();
                $periodStart = $periodEnd->copy()->subDays(6)->startOfDay();

                $amount = fake()->randomFloat(2, 500, 15000);
                $status = $week > 2
                    ? 'paid'
                    : ($week === 2 ? fake()->randomElement(['pending', 'processing', 'paid']) : 'pending');

                DB::table('vendor_payouts')->insert([
                    'vendor_id' => $vendor->id,
                    'amount' => $amount,
                    'payout_method' => fake()->randomElement($payoutMethods),
                    'status' => $status,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'paid_at' => $status === 'paid' ? $periodEnd->copy()->addDays(rand(1, 3)) : null,
                    'created_at' => $periodEnd,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
