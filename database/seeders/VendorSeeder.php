<?php

namespace Database\Seeders;

use App\CompanyType;
use App\Models\User;
use App\Models\Vendor;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Koton',
                'slug' => 'koton',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/koton/koton_1598857541927.jpg',
                'description' => 'Koton, 1988 yılından bu yana Türkiye\'nin önde gelen moda markalarından biridir.',
                'phone' => '05321234567',
                'email' => 'koton@sirizen.com',
                'rating' => 4.8,
                'response_time' => 60,
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/nike/nike_1598857541927.jpg',
                'description' => 'Nike, dünya\'nın en büyük spor giyim ve ayakkabı markasıdır.',
                'phone' => '05321112233',
                'email' => 'nike@sirizen.com',
                'rating' => 4.9,
                'response_time' => 30,
            ],
        ];

        foreach ($vendors as $vendorData) {
            $user = User::updateOrCreate(
                ['email' => $vendorData['email']],
                [
                    'name' => $vendorData['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::VENDOR,
                    'phone' => $vendorData['phone'],
                ]
            );

            Vendor::updateOrCreate(
                ['slug' => $vendorData['slug']],
                [
                    'user_id' => $user->id,
                    'tier_id' => 3,
                    'name' => $vendorData['name'],
                    'description' => $vendorData['description'],
                    'company_type' => CompanyType::LIMITED->value,
                    'tax_number' => '12345678' . str_pad((string) crc32($vendorData['slug']), 2, '0', STR_PAD_LEFT),
                    'city' => 'İstanbul',
                    'district' => 'Kadıköy',
                    'rating' => $vendorData['rating'],
                    'total_orders' => 1000,
                    'followers' => 0,
                    'response_time_avg' => $vendorData['response_time'],
                    'cancel_rate' => 1.0,
                    'return_rate' => 2.0,
                    'late_shipment_rate' => 1.0,
                    'status' => 'active',
                ]
            );
        }
    }
}
