<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Get customer users (not admin or vendors)
        $customers = User::where('role', UserRole::CUSTOMER)->get();

        $addresses = [
            [
                'title' => 'Ev',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'address_line' => 'Caferağa Mahallesi, Moda Caddesi No:45 D:7',
                'postal_code' => '34710',
                'is_default' => true,
            ],
            [
                'title' => 'İş',
                'city' => 'İstanbul',
                'district' => 'Şişli',
                'address_line' => 'Mecidiyeköy Mahallesi, Büyükdere Caddesi No:12 Kat:5',
                'postal_code' => '34394',
                'is_default' => false,
            ],
            [
                'title' => 'Ev',
                'city' => 'Ankara',
                'district' => 'Çankaya',
                'address_line' => 'Kızılay Mahallesi, Atatürk Bulvarı No:88 D:3',
                'postal_code' => '06420',
                'is_default' => true,
            ],
            [
                'title' => 'Ev',
                'city' => 'İzmir',
                'district' => 'Karşıyaka',
                'address_line' => 'Bostanlı Mahallesi, Cemal Gürsel Caddesi No:156 D:12',
                'postal_code' => '35550',
                'is_default' => true,
            ],
            [
                'title' => 'Ev',
                'city' => 'Antalya',
                'district' => 'Muratpaşa',
                'address_line' => 'Lara Mahallesi, Atatürk Parkı Sokak No:23 D:6',
                'postal_code' => '07100',
                'is_default' => true,
            ],
        ];

        foreach ($customers as $index => $customer) {
            if (isset($addresses[$index])) {
                Address::updateOrCreate(
                    ['user_id' => $customer->id, 'title' => $addresses[$index]['title']],
                    $addresses[$index]
                );
            }
        }
    }
}
