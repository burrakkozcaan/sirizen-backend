<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorScore;
use Illuminate\Database\Seeder;

class VendorScoreSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            return;
        }

        foreach ($vendors as $vendor) {
            // Trendyol gibi satıcı skorları: Teslimat, Puan, Stok, Destek
            $deliveryScore = rand(70, 100) / 10; // 7.0-10.0
            $ratingScore = rand(70, 100) / 10;   // 7.0-10.0
            $stockScore = rand(70, 100) / 10;    // 7.0-10.0
            $supportScore = rand(70, 100) / 10;  // 7.0-10.0

            $totalScore = ($deliveryScore + $ratingScore + $stockScore + $supportScore) / 4;

            VendorScore::create([
                'vendor_id' => $vendor->id,
                'total_score' => round($totalScore, 2),
                'delivery_score' => $deliveryScore,
                'rating_score' => $ratingScore,
                'stock_score' => $stockScore,
                'support_score' => $supportScore,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
