<?php

namespace Database\Seeders;

use App\Models\ReturnPolicy;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ReturnPolicySeeder extends Seeder
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

        $templates = [
            [
                'days' => 14,
                'is_free' => true,
                'conditions' => 'Ürün kullanılmamış ve orijinal ambalajında olmalıdır.',
            ],
            [
                'days' => 30,
                'is_free' => true,
                'conditions' => 'Etiket ve fatura ile birlikte iade edilebilir.',
            ],
            [
                'days' => 7,
                'is_free' => false,
                'conditions' => 'İade kargo ücreti müşteriye aittir.',
            ],
        ];

        foreach ($vendors as $index => $vendor) {
            $template = $templates[$index % count($templates)];

            ReturnPolicy::updateOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'vendor_id' => $vendor->id,
                    ...$template,
                ]
            );
        }
    }
}
