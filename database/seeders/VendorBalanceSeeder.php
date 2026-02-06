<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorBalance;
use Illuminate\Database\Seeder;

class VendorBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            return;
        }

        foreach ($vendors as $vendor) {
            // Satıcı bakiyeleri (Trendyol gibi)
            $totalEarnings = rand(50000, 500000); // Toplam kazanç
            $totalWithdrawn = rand(20000, $totalEarnings * 0.7); // Çekilen miktar
            $pendingBalance = rand(5000, 30000); // Bekleyen bakiye (teslim edilmemiş siparişler)
            $availableBalance = $totalEarnings - $totalWithdrawn - $pendingBalance; // Kullanılabilir bakiye
            $balance = $availableBalance + $pendingBalance; // Toplam bakiye

            VendorBalance::create([
                'vendor_id' => $vendor->id,
                'balance' => $balance,
                'available_balance' => max(0, $availableBalance), // Negatif olmamalı
                'pending_balance' => $pendingBalance,
                'total_earnings' => $totalEarnings,
                'total_withdrawn' => $totalWithdrawn,
                'currency' => 'TRY',
                'last_settlement_at' => now()->subDays(rand(1, 15)),
                'created_at' => now()->subMonths(rand(1, 6)),
            ]);
        }
    }
}
