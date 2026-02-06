<?php

namespace Database\Seeders;

use App\Models\ShippingRule;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ShippingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            return;
        }

        foreach ($vendors as $index => $vendor) {
            $sameDay = $index % 2 === 0;
            $freeShipping = $index % 3 === 0;

            ShippingRule::updateOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'vendor_id' => $vendor->id,
                    'cutoff_time' => $sameDay ? '16:00:00' : '14:00:00',
                    'same_day_shipping' => $sameDay,
                    'free_shipping' => $freeShipping,
                    'free_shipping_min_amount' => $freeShipping ? fake()->randomFloat(2, 250, 750) : null,
                ]
            );
        }
    }
}
