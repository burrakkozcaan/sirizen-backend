<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorFollower;
use App\UserRole;
use Illuminate\Database\Seeder;

class VendorFollowerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', UserRole::CUSTOMER)->get();
        $vendors = Vendor::all();

        if ($customers->isEmpty() || $vendors->isEmpty()) {
            return;
        }

        // Her müşteri 2-4 satıcıyı takip etsin
        foreach ($customers as $customer) {
            $followCount = rand(2, 4);
            $selectedVendors = $vendors->random(min($followCount, $vendors->count()));

            foreach ($selectedVendors as $vendor) {
                $created = VendorFollower::firstOrCreate(
                    ['user_id' => $customer->id, 'vendor_id' => $vendor->id],
                    ['created_at' => now()->subDays(rand(1, 90))]
                );

                // Satıcının followers sayısını güncelle (only if newly created)
                if ($created->wasRecentlyCreated) {
                    $vendor->increment('followers');
                }
            }
        }
    }
}
