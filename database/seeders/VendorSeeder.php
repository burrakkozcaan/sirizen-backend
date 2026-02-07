<?php

namespace Database\Seeders;

use App\CompanyType;
use App\Models\User;
use App\Models\Vendor;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'id' => 1,
                'name' => 'Koton',
                'slug' => 'koton',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/koton/koton_1598857541927.jpg',
                'banner' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200',
                'description' => 'Koton, 1988 yılından bu yana Türkiye\'nin önde gelen moda markalarından biridir.',
                'location' => 'İstanbul, Türkiye',
                'phone' => '05321234567',
                'email' => 'koton@sirizen.com',
                'rating' => 4.8,
                'review_count' => 125000,
                'follower_count' => 2500000,
                'product_count' => 8500,
                'response_time' => 60,
                'is_official' => true,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'LC Waikiki',
                'slug' => 'lc-waikiki',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/lcwaikiki/lcwaikiki_1598857541927.jpg',
                'banner' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=1200',
                'description' => 'LC Waikiki, herkes için ulaşılabilir moda anlayışıyla hizmet vermektedir.',
                'location' => 'İstanbul, Türkiye',
                'phone' => '05329876543',
                'email' => 'lcwaikiki@sirizen.com',
                'rating' => 4.7,
                'review_count' => 98000,
                'follower_count' => 1800000,
                'product_count' => 12000,
                'response_time' => 120,
                'is_official' => true,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Nike',
                'slug' => 'nike',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/nike/nike_1598857541927.jpg',
                'banner' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1200',
                'description' => 'Nike, dünya\'nın en büyük spor giyim ve ayakkabı markasıdır.',
                'location' => 'İstanbul, Türkiye',
                'phone' => '05321112233',
                'email' => 'nike@sirizen.com',
                'rating' => 4.9,
                'review_count' => 85000,
                'follower_count' => 3200000,
                'product_count' => 4500,
                'response_time' => 30,
                'is_official' => true,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Zara',
                'slug' => 'zara',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/zara/zara_1598857541927.jpg',
                'banner' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=1200',
                'description' => 'Zara, İspanya merkezli uluslararası moda markasıdır.',
                'location' => 'İstanbul, Türkiye',
                'phone' => '05324445566',
                'email' => 'zara@sirizen.com',
                'rating' => 4.6,
                'review_count' => 72000,
                'follower_count' => 2100000,
                'product_count' => 6800,
                'response_time' => 60,
                'is_official' => true,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Mavi',
                'slug' => 'mavi',
                'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/mavi/mavi_1598857541927.jpg',
                'banner' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=1200',
                'description' => 'Mavi, 1991\'den beri denim ve casual giyimde öncü bir Türk markasıdır.',
                'location' => 'İstanbul, Türkiye',
                'phone' => '05327778899',
                'email' => 'mavi@sirizen.com',
                'rating' => 4.7,
                'review_count' => 65000,
                'follower_count' => 1500000,
                'product_count' => 5200,
                'response_time' => 45,
                'is_official' => true,
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendorData) {
            // Create user for vendor
            $user = User::updateOrCreate(
                ['email' => $vendorData['email']],
                [
                    'name' => $vendorData['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::VENDOR,
                    'phone' => $vendorData['phone'],
                ]
            );

            // Tier ataması: Büyük markalar için Altın (3), diğerleri için Gümüş (2)
            $totalOrders = $vendorData['follower_count'] / 1000; // Takipçilere göre sipariş tahmini
            $tierId = match (true) {
                $totalOrders >= 2000 => 4, // Elmas
                $totalOrders >= 500 => 3,  // Altın
                $totalOrders >= 100 => 2,  // Gümüş
                default => 1,              // Bronz
            };

            // Şirket türü ataması (rastgele)
            $companyTypes = [CompanyType::LIMITED, CompanyType::ANONIM, CompanyType::SAHIS];
            $companyType = $companyTypes[array_rand($companyTypes)];

            // Create vendor with only existing columns
            Vendor::updateOrCreate(
                ['slug' => $vendorData['slug']],
                [
                    'id' => $vendorData['id'],
                    'user_id' => $user->id,
                    'tier_id' => $tierId,
                    'name' => $vendorData['name'],
                    'description' => $vendorData['description'],
                    'company_type' => $companyType->value,
                    'tax_number' => '12345678'.str_pad((string) $vendorData['id'], 2, '0', STR_PAD_LEFT),
                    'city' => ['İstanbul', 'Ankara', 'İzmir'][array_rand(['İstanbul', 'Ankara', 'İzmir'])],
                    'district' => ['Kadıköy', 'Çankaya', 'Karşıyaka'][array_rand(['Kadıköy', 'Çankaya', 'Karşıyaka'])],
                    'rating' => $vendorData['rating'],
                    'total_orders' => (int) $totalOrders,
                    'followers' => 0, // VendorFollowerSeeder tarafından güncellenecek
                    'response_time_avg' => $vendorData['response_time'],
                    'cancel_rate' => rand(0, 20) / 10,  // 0-2% arası
                    'return_rate' => rand(0, 50) / 10,  // 0-5% arası
                    'late_shipment_rate' => rand(0, 30) / 10, // 0-3% arası
                    'status' => $vendorData['is_active'] ? 'active' : 'inactive',
                ]
            );
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            "SELECT setval(pg_get_serial_sequence('vendors', 'id'), (SELECT COALESCE(MAX(id), 0) FROM vendors))"
        );
    }
}
