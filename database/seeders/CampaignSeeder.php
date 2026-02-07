<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'title' => 'Kış İndirimi',
                'slug' => 'kis-indirimi',
                'description' => 'Sezon sonu büyük indirim! %50\'ye varan fırsatlar.',
                'banner' => 'https://images.unsplash.com/photo-1607083206869-4c7672e72a8a?w=1600',
                'discount_type' => 'percentage',
                'discount_value' => 50,
                'starts_at' => '2026-01-01 00:00:00',
                'ends_at' => '2026-06-30 23:59:59',
                'is_active' => true,
            ],
            [
                'title' => 'Ayakkabı Festivali',
                'slug' => 'ayakkabi-festivali',
                'description' => 'Tüm ayakkabılarda %30 indirim!',
                'banner' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=1600',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'starts_at' => '2026-01-15 00:00:00',
                'ends_at' => '2026-06-15 23:59:59',
                'is_active' => true,
            ],
            [
                'title' => 'Elektronik Fırsatları',
                'slug' => 'elektronik-firsatlari',
                'description' => 'Elektronik ürünlerde kaçırılmayacak fiyatlar!',
                'banner' => 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?w=1600',
                'discount_type' => 'percentage',
                'discount_value' => 40,
                'starts_at' => '2026-01-10 00:00:00',
                'ends_at' => '2026-06-30 23:59:59',
                'is_active' => true,
            ],
            [
                'title' => 'Kozmetik Günleri',
                'slug' => 'kozmetik-gunleri',
                'description' => 'En sevilen kozmetik markaları indirimde!',
                'banner' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1600',
                'discount_type' => 'percentage',
                'discount_value' => 35,
                'starts_at' => '2026-01-20 00:00:00',
                'ends_at' => '2026-06-20 23:59:59',
                'is_active' => true,
            ],
            [
                'title' => 'Bahar Koleksiyonu',
                'slug' => 'bahar-koleksiyonu',
                'description' => 'Yeni sezon ürünlerinde %25 indirim!',
                'banner' => 'https://images.unsplash.com/photo-1490750967868-88aa4f44baee?w=1600',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'starts_at' => '2026-02-01 00:00:00',
                'ends_at' => '2026-05-31 23:59:59',
                'is_active' => true,
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::updateOrCreate(
                ['slug' => $campaign['slug']],
                $campaign
            );
        }
    }
}
