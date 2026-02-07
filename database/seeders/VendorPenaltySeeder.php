<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorPenalty;
use Illuminate\Database\Seeder;

class VendorPenaltySeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            return;
        }

        $penaltyReasons = [
            'Geç teslimat nedeniyle ceza',
            'Stok bilgisi hatalı',
            'Ürün açıklaması eksik',
            'Müşteri şikayeti',
            'İptal oranı yüksek',
        ];

        // Her satıcıdan %40'ına rastgele ceza ekle
        $vendorsWithPenalty = $vendors->random((int) ceil($vendors->count() * 0.4));

        foreach ($vendorsWithPenalty as $vendor) {
            $penaltyCount = rand(1, 2);

            for ($i = 0; $i < $penaltyCount; $i++) {
                $reason = $penaltyReasons[array_rand($penaltyReasons)];
                VendorPenalty::firstOrCreate(
                    ['vendor_id' => $vendor->id, 'reason' => $reason],
                    [
                        'penalty_points' => rand(5, 25),
                        'expires_at' => now()->addDays(rand(30, 90)),
                        'created_at' => now()->subDays(rand(1, 30)),
                    ]
                );
            }
        }
    }
}
