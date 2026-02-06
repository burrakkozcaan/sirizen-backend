<?php

namespace Database\Seeders;

use App\Models\ShippingCompany;
use Illuminate\Database\Seeder;

class ShippingCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Yurtiçi Kargo',
                'code' => 'yurtici',
                'tracking_url' => 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code={tracking_number}',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Aras Kargo',
                'code' => 'aras',
                'tracking_url' => 'https://www.araskargo.com.tr/tr/kargo-takip?code={tracking_number}',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'MNG Kargo',
                'code' => 'mng',
                'tracking_url' => 'https://www.mngkargo.com.tr/tracking?code={tracking_number}',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'PTT Kargo',
                'code' => 'ptt',
                'tracking_url' => 'https://gonderitakip.ptt.gov.tr/Track/Verify?q={tracking_number}',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Sürat Kargo',
                'code' => 'surat',
                'tracking_url' => 'https://www.suratkargo.com.tr/kargo-takip?code={tracking_number}',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'UPS Kargo',
                'code' => 'ups',
                'tracking_url' => 'https://www.ups.com/track?tracknum={tracking_number}',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'DHL',
                'code' => 'dhl',
                'tracking_url' => 'https://www.dhl.com/tr-tr/home/tracking.html?tracking-id={tracking_number}',
                'is_active' => true,
                'order' => 7,
            ],
        ];

        foreach ($companies as $company) {
            ShippingCompany::updateOrCreate(
                ['code' => $company['code']],
                $company
            );
        }
    }
}
