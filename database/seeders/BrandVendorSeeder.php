<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class BrandVendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();
        $brands = Brand::whereNull('vendor_id')->get(); // Get established brands (non-vendor brands)

        if ($vendors->isEmpty() || $brands->isEmpty()) {
            return;
        }

        // Create vendor-owned brands (2-3 vendors will have their own brands)
        $vendorOwnedBrands = [
            ['name' => 'Özgün Moda', 'slug' => 'ozgun-moda'],
            ['name' => 'Elit Giyim', 'slug' => 'elit-giyim'],
            ['name' => 'Şık Butik', 'slug' => 'sik-butik'],
        ];

        $vendorIndex = 0;
        foreach ($vendors->take(3) as $vendor) {
            if (isset($vendorOwnedBrands[$vendorIndex])) {
                Brand::updateOrCreate(
                    ['slug' => $vendorOwnedBrands[$vendorIndex]['slug']],
                    [
                        'name' => $vendorOwnedBrands[$vendorIndex]['name'],
                        'vendor_id' => $vendor->id,
                        'is_vendor_brand' => true,
                    ]
                );
                $vendorIndex++;
            }
        }

        // Authorize vendors to sell established brands
        // Each vendor gets authorized for 2-5 random brands
        foreach ($vendors as $vendor) {
            $authorizedBrands = $brands->random(min(rand(2, 5), $brands->count()));

            foreach ($authorizedBrands as $brand) {
                $vendor->authorizedBrands()->syncWithoutDetaching([
                    $brand->id => [
                        'is_authorized' => true,
                        'authorized_at' => now()->subDays(rand(1, 180)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        }
    }
}
