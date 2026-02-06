<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            // ========================================
            // GİYİM - Kadın Giyim (attribute_set_id: 1)
            // ========================================
            [
                'attribute_set_id' => 1,
                'key' => 'beden',
                'label' => 'Beden',
                'type' => 'select',
                'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 1,
                'key' => 'renk',
                'label' => 'Renk',
                'type' => 'select',
                'options' => [
                    ['value' => 'siyah', 'label' => 'Siyah', 'hex' => '#000000'],
                    ['value' => 'beyaz', 'label' => 'Beyaz', 'hex' => '#FFFFFF'],
                    ['value' => 'kirmizi', 'label' => 'Kırmızı', 'hex' => '#EF4444'],
                    ['value' => 'mavi', 'label' => 'Mavi', 'hex' => '#3B82F6'],
                    ['value' => 'yesil', 'label' => 'Yeşil', 'hex' => '#22C55E'],
                    ['value' => 'sari', 'label' => 'Sarı', 'hex' => '#EAB308'],
                    ['value' => 'pembe', 'label' => 'Pembe', 'hex' => '#EC4899'],
                    ['value' => 'mor', 'label' => 'Mor', 'hex' => '#A855F7'],
                    ['value' => 'turuncu', 'label' => 'Turuncu', 'hex' => '#F97316'],
                    ['value' => 'gri', 'label' => 'Gri', 'hex' => '#6B7280'],
                    ['value' => 'lacivert', 'label' => 'Lacivert', 'hex' => '#1E3A8A'],
                    ['value' => 'bej', 'label' => 'Bej', 'hex' => '#D4B896'],
                    ['value' => 'kahverengi', 'label' => 'Kahverengi', 'hex' => '#92400E'],
                ],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 1,
                'key' => 'kumas',
                'label' => 'Kumaş',
                'type' => 'select',
                'options' => ['Pamuk', 'Polyester', 'Viskon', 'Keten', 'Denim', 'Kadife', 'İpek', 'Yün'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 1,
                'key' => 'kalip',
                'label' => 'Kalıp',
                'type' => 'select',
                'options' => ['Regular Fit', 'Slim Fit', 'Oversize', 'Loose Fit'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],
            [
                'attribute_set_id' => 1,
                'key' => 'kol-boyu',
                'label' => 'Kol Boyu',
                'type' => 'select',
                'options' => ['Uzun Kol', 'Kısa Kol', 'Kolsuz', '3/4 Kol'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 5,
            ],
            [
                'attribute_set_id' => 1,
                'key' => 'yaka-tipi',
                'label' => 'Yaka Tipi',
                'type' => 'select',
                'options' => ['V Yaka', 'Bisiklet Yaka', 'Polo Yaka', 'Gömlek Yaka', 'Dik Yaka'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 6,
            ],

            // ========================================
            // GİYİM - Erkek Giyim (attribute_set_id: 2)
            // ========================================
            [
                'attribute_set_id' => 2,
                'key' => 'beden',
                'label' => 'Beden',
                'type' => 'select',
                'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 2,
                'key' => 'renk',
                'label' => 'Renk',
                'type' => 'select',
                'options' => [
                    ['value' => 'siyah', 'label' => 'Siyah', 'hex' => '#000000'],
                    ['value' => 'beyaz', 'label' => 'Beyaz', 'hex' => '#FFFFFF'],
                    ['value' => 'mavi', 'label' => 'Mavi', 'hex' => '#3B82F6'],
                    ['value' => 'gri', 'label' => 'Gri', 'hex' => '#6B7280'],
                    ['value' => 'lacivert', 'label' => 'Lacivert', 'hex' => '#1E3A8A'],
                    ['value' => 'kahverengi', 'label' => 'Kahverengi', 'hex' => '#92400E'],
                    ['value' => 'yesil', 'label' => 'Yeşil', 'hex' => '#22C55E'],
                    ['value' => 'bordo', 'label' => 'Bordo', 'hex' => '#881337'],
                ],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 2,
                'key' => 'kumas',
                'label' => 'Kumaş',
                'type' => 'select',
                'options' => ['Pamuk', 'Polyester', 'Keten', 'Denim', 'Yün'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 2,
                'key' => 'kalip',
                'label' => 'Kalıp',
                'type' => 'select',
                'options' => ['Regular Fit', 'Slim Fit', 'Oversize', 'Comfort Fit'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],

            // ========================================
            // GİYİM - Dış Giyim (attribute_set_id: 3)
            // ========================================
            [
                'attribute_set_id' => 3,
                'key' => 'beden',
                'label' => 'Beden',
                'type' => 'select',
                'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 3,
                'key' => 'renk',
                'label' => 'Renk',
                'type' => 'select',
                'options' => [
                    ['value' => 'siyah', 'label' => 'Siyah', 'hex' => '#000000'],
                    ['value' => 'lacivert', 'label' => 'Lacivert', 'hex' => '#1E3A8A'],
                    ['value' => 'kahverengi', 'label' => 'Kahverengi', 'hex' => '#92400E'],
                    ['value' => 'bej', 'label' => 'Bej', 'hex' => '#D4B896'],
                    ['value' => 'haki', 'label' => 'Haki', 'hex' => '#6B8E23'],
                ],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 3,
                'key' => 'deri-turu',
                'label' => 'Deri Türü',
                'type' => 'select',
                'options' => ['Gerçek Deri', 'Suni Deri', 'Süet', 'Nubuk'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 3,
                'key' => 'astarli-mi',
                'label' => 'Astarlı mı?',
                'type' => 'boolean',
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],
            [
                'attribute_set_id' => 3,
                'key' => 'mevsim',
                'label' => 'Mevsim',
                'type' => 'select',
                'options' => ['Kış', 'Sonbahar', 'İlkbahar', '4 Mevsim'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 5,
            ],

            // ========================================
            // ELEKTRONİK - Telefon (attribute_set_id: 4)
            // ========================================
            [
                'attribute_set_id' => 4,
                'key' => 'dahili-hafiza',
                'label' => 'Dahili Hafıza',
                'type' => 'select',
                'options' => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB'],
                'unit' => 'GB',
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 4,
                'key' => 'ram',
                'label' => 'RAM',
                'type' => 'select',
                'options' => ['4 GB', '6 GB', '8 GB', '12 GB', '16 GB'],
                'unit' => 'GB',
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 4,
                'key' => 'ekran-boyutu',
                'label' => 'Ekran Boyutu',
                'type' => 'select',
                'options' => ['5.5"', '6.1"', '6.5"', '6.7"', '6.9"'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 4,
                'key' => 'kamera',
                'label' => 'Kamera',
                'type' => 'select',
                'options' => ['12 MP', '48 MP', '50 MP', '108 MP', '200 MP'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],
            [
                'attribute_set_id' => 4,
                'key' => 'isletim-sistemi',
                'label' => 'İşletim Sistemi',
                'type' => 'select',
                'options' => ['iOS', 'Android'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 5,
            ],
            [
                'attribute_set_id' => 4,
                'key' => 'garanti-suresi',
                'label' => 'Garanti Süresi',
                'type' => 'select',
                'options' => ['12 Ay', '24 Ay', '36 Ay'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 6,
            ],

            // ========================================
            // ELEKTRONİK - Bilgisayar (attribute_set_id: 5)
            // ========================================
            [
                'attribute_set_id' => 5,
                'key' => 'islemci',
                'label' => 'İşlemci',
                'type' => 'select',
                'options' => ['Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9', 'Apple M1', 'Apple M2', 'Apple M3'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 5,
                'key' => 'ram',
                'label' => 'RAM',
                'type' => 'select',
                'options' => ['8 GB', '16 GB', '32 GB', '64 GB'],
                'unit' => 'GB',
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 5,
                'key' => 'depolama',
                'label' => 'Depolama',
                'type' => 'select',
                'options' => ['256 GB SSD', '512 GB SSD', '1 TB SSD', '2 TB SSD', '1 TB HDD', '2 TB HDD'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 5,
                'key' => 'ekran-boyutu',
                'label' => 'Ekran Boyutu',
                'type' => 'select',
                'options' => ['13.3"', '14"', '15.6"', '16"', '17.3"'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],
            [
                'attribute_set_id' => 5,
                'key' => 'ekran-karti',
                'label' => 'Ekran Kartı',
                'type' => 'select',
                'options' => ['Entegre', 'NVIDIA GTX', 'NVIDIA RTX', 'AMD Radeon'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 5,
            ],

            // ========================================
            // KOZMETİK - Makyaj (attribute_set_id: 7)
            // ========================================
            [
                'attribute_set_id' => 7,
                'key' => 'cilt-tipi',
                'label' => 'Cilt Tipi',
                'type' => 'multiselect',
                'options' => ['Normal', 'Kuru', 'Yağlı', 'Karma', 'Hassas'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 7,
                'key' => 'ton',
                'label' => 'Ton',
                'type' => 'select',
                'options' => ['Açık', 'Orta', 'Koyu', 'Çok Koyu'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 7,
                'key' => 'finish',
                'label' => 'Finish',
                'type' => 'select',
                'options' => ['Mat', 'Saten', 'Parlak', 'Işıltılı'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],

            // ========================================
            // AYAKKABI (attribute_set_id: 14)
            // ========================================
            [
                'attribute_set_id' => 14,
                'key' => 'numara',
                'label' => 'Numara',
                'type' => 'select',
                'options' => ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 1,
            ],
            [
                'attribute_set_id' => 14,
                'key' => 'renk',
                'label' => 'Renk',
                'type' => 'select',
                'options' => [
                    ['value' => 'siyah', 'label' => 'Siyah', 'hex' => '#000000'],
                    ['value' => 'beyaz', 'label' => 'Beyaz', 'hex' => '#FFFFFF'],
                    ['value' => 'kahverengi', 'label' => 'Kahverengi', 'hex' => '#92400E'],
                    ['value' => 'lacivert', 'label' => 'Lacivert', 'hex' => '#1E3A8A'],
                    ['value' => 'bej', 'label' => 'Bej', 'hex' => '#D4B896'],
                ],
                'is_filterable' => true,
                'is_required' => true,
                'order' => 2,
            ],
            [
                'attribute_set_id' => 14,
                'key' => 'topuk-yuksekligi',
                'label' => 'Topuk Yüksekliği',
                'type' => 'select',
                'options' => ['Düz', '2-4 cm', '5-7 cm', '8-10 cm', '10+ cm'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'attribute_set_id' => 14,
                'key' => 'malzeme',
                'label' => 'Malzeme',
                'type' => 'select',
                'options' => ['Deri', 'Süet', 'Tekstil', 'Sentetik'],
                'is_filterable' => true,
                'is_required' => false,
                'order' => 4,
            ],
        ];

        foreach ($attributes as $attribute) {
            Attribute::updateOrCreate(
                [
                    'attribute_set_id' => $attribute['attribute_set_id'],
                    'key' => $attribute['key'],
                ],
                array_merge($attribute, ['is_active' => true])
            );
        }
    }
}
