<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\FilterConfig;
use Illuminate\Database\Seeder;

class FilterConfigSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // GİYİM (category_group_id: 1)
        // ========================================
        $giyimFilters = [
            // Sistem filtreleri
            [
                'category_group_id' => 1,
                'filter_type' => 'price',
                'display_label' => 'Fiyat',
                'filter_component' => 'range',
                'order' => 1,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => ['min' => 0, 'max' => 5000, 'step' => 50, 'unit' => 'TL'],
            ],
            [
                'category_group_id' => 1,
                'filter_type' => 'brand',
                'display_label' => 'Marka',
                'filter_component' => 'checkbox',
                'order' => 2,
                'is_collapsed' => false,
                'show_count' => true,
            ],
            [
                'category_group_id' => 1,
                'filter_type' => 'rating',
                'display_label' => 'Değerlendirme',
                'filter_component' => 'rating',
                'order' => 10,
                'is_collapsed' => true,
                'show_count' => false,
            ],
            [
                'category_group_id' => 1,
                'filter_type' => 'seller',
                'display_label' => 'Satıcı',
                'filter_component' => 'checkbox',
                'order' => 11,
                'is_collapsed' => true,
                'show_count' => true,
            ],
            [
                'category_group_id' => 1,
                'filter_type' => 'shipping',
                'display_label' => 'Kargo',
                'filter_component' => 'checkbox',
                'order' => 12,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => [
                    'options' => [
                        ['value' => 'free_shipping', 'label' => 'Ücretsiz Kargo'],
                        ['value' => 'fast_delivery', 'label' => 'Hızlı Teslimat'],
                    ],
                ],
            ],
        ];

        // Giyim attribute filtreleri - beden, renk, kumaş, kalıp
        $giyimAttributeFilters = [
            ['key' => 'beden', 'label' => 'Beden', 'component' => 'checkbox', 'order' => 3],
            ['key' => 'renk', 'label' => 'Renk', 'component' => 'color', 'order' => 4],
            ['key' => 'kumas', 'label' => 'Kumaş', 'component' => 'checkbox', 'order' => 5],
            ['key' => 'kalip', 'label' => 'Kalıp', 'component' => 'checkbox', 'order' => 6],
        ];

        foreach ($giyimAttributeFilters as $filter) {
            $attribute = Attribute::where('key', $filter['key'])
                ->whereHas('attributeSet', fn ($q) => $q->where('category_group_id', 1))
                ->first();

            if ($attribute) {
                $giyimFilters[] = [
                    'category_group_id' => 1,
                    'filter_type' => 'attribute',
                    'attribute_id' => $attribute->id,
                    'display_label' => $filter['label'],
                    'filter_component' => $filter['component'],
                    'order' => $filter['order'],
                    'is_collapsed' => $filter['order'] > 5,
                    'show_count' => true,
                ];
            }
        }

        // ========================================
        // ELEKTRONİK (category_group_id: 2)
        // ========================================
        $elektronikFilters = [
            [
                'category_group_id' => 2,
                'filter_type' => 'price',
                'display_label' => 'Fiyat',
                'filter_component' => 'range',
                'order' => 1,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => ['min' => 0, 'max' => 100000, 'step' => 500, 'unit' => 'TL'],
            ],
            [
                'category_group_id' => 2,
                'filter_type' => 'brand',
                'display_label' => 'Marka',
                'filter_component' => 'checkbox',
                'order' => 2,
                'is_collapsed' => false,
                'show_count' => true,
            ],
            [
                'category_group_id' => 2,
                'filter_type' => 'rating',
                'display_label' => 'Değerlendirme',
                'filter_component' => 'rating',
                'order' => 10,
                'is_collapsed' => true,
                'show_count' => false,
            ],
            [
                'category_group_id' => 2,
                'filter_type' => 'seller',
                'display_label' => 'Satıcı',
                'filter_component' => 'checkbox',
                'order' => 11,
                'is_collapsed' => true,
                'show_count' => true,
            ],
            [
                'category_group_id' => 2,
                'filter_type' => 'shipping',
                'display_label' => 'Kargo',
                'filter_component' => 'checkbox',
                'order' => 12,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => [
                    'options' => [
                        ['value' => 'free_shipping', 'label' => 'Ücretsiz Kargo'],
                        ['value' => 'fast_delivery', 'label' => 'Hızlı Teslimat'],
                    ],
                ],
            ],
        ];

        // Elektronik attribute filtreleri
        $elektronikAttributeFilters = [
            ['key' => 'ram', 'label' => 'RAM', 'component' => 'checkbox', 'order' => 3],
            ['key' => 'dahili-hafiza', 'label' => 'Dahili Hafıza', 'component' => 'checkbox', 'order' => 4],
            ['key' => 'ekran-boyutu', 'label' => 'Ekran Boyutu', 'component' => 'checkbox', 'order' => 5],
            ['key' => 'isletim-sistemi', 'label' => 'İşletim Sistemi', 'component' => 'checkbox', 'order' => 6],
            ['key' => 'garanti-suresi', 'label' => 'Garanti Süresi', 'component' => 'checkbox', 'order' => 7],
        ];

        foreach ($elektronikAttributeFilters as $filter) {
            $attribute = Attribute::where('key', $filter['key'])
                ->whereHas('attributeSet', fn ($q) => $q->where('category_group_id', 2))
                ->first();

            if ($attribute) {
                $elektronikFilters[] = [
                    'category_group_id' => 2,
                    'filter_type' => 'attribute',
                    'attribute_id' => $attribute->id,
                    'display_label' => $filter['label'],
                    'filter_component' => $filter['component'],
                    'order' => $filter['order'],
                    'is_collapsed' => $filter['order'] > 5,
                    'show_count' => true,
                ];
            }
        }

        // ========================================
        // KOZMETİK (category_group_id: 3)
        // ========================================
        $kozmetikFilters = [
            [
                'category_group_id' => 3,
                'filter_type' => 'price',
                'display_label' => 'Fiyat',
                'filter_component' => 'range',
                'order' => 1,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => ['min' => 0, 'max' => 3000, 'step' => 25, 'unit' => 'TL'],
            ],
            [
                'category_group_id' => 3,
                'filter_type' => 'brand',
                'display_label' => 'Marka',
                'filter_component' => 'checkbox',
                'order' => 2,
                'is_collapsed' => false,
                'show_count' => true,
            ],
            [
                'category_group_id' => 3,
                'filter_type' => 'rating',
                'display_label' => 'Değerlendirme',
                'filter_component' => 'rating',
                'order' => 6,
                'is_collapsed' => true,
                'show_count' => false,
            ],
            [
                'category_group_id' => 3,
                'filter_type' => 'shipping',
                'display_label' => 'Kargo',
                'filter_component' => 'checkbox',
                'order' => 7,
                'is_collapsed' => false,
                'show_count' => false,
                'config' => [
                    'options' => [
                        ['value' => 'free_shipping', 'label' => 'Ücretsiz Kargo'],
                    ],
                ],
            ],
        ];

        // Kozmetik attribute filtreleri
        $kozmetikAttributeFilters = [
            ['key' => 'cilt-tipi', 'label' => 'Cilt Tipi', 'component' => 'checkbox', 'order' => 3],
            ['key' => 'ton', 'label' => 'Ton', 'component' => 'checkbox', 'order' => 4],
            ['key' => 'finish', 'label' => 'Finish', 'component' => 'checkbox', 'order' => 5],
        ];

        foreach ($kozmetikAttributeFilters as $filter) {
            $attribute = Attribute::where('key', $filter['key'])
                ->whereHas('attributeSet', fn ($q) => $q->where('category_group_id', 3))
                ->first();

            if ($attribute) {
                $kozmetikFilters[] = [
                    'category_group_id' => 3,
                    'filter_type' => 'attribute',
                    'attribute_id' => $attribute->id,
                    'display_label' => $filter['label'],
                    'filter_component' => $filter['component'],
                    'order' => $filter['order'],
                    'is_collapsed' => false,
                    'show_count' => true,
                ];
            }
        }

        // ========================================
        // DİĞER KATEGORİ GRUPLARI İÇİN DEFAULT FİLTRELER
        // ========================================
        $defaultFilters = function ($categoryGroupId, $maxPrice = 10000) {
            return [
                [
                    'category_group_id' => $categoryGroupId,
                    'filter_type' => 'price',
                    'display_label' => 'Fiyat',
                    'filter_component' => 'range',
                    'order' => 1,
                    'is_collapsed' => false,
                    'show_count' => false,
                    'config' => ['min' => 0, 'max' => $maxPrice, 'step' => 50, 'unit' => 'TL'],
                ],
                [
                    'category_group_id' => $categoryGroupId,
                    'filter_type' => 'brand',
                    'display_label' => 'Marka',
                    'filter_component' => 'checkbox',
                    'order' => 2,
                    'is_collapsed' => false,
                    'show_count' => true,
                ],
                [
                    'category_group_id' => $categoryGroupId,
                    'filter_type' => 'rating',
                    'display_label' => 'Değerlendirme',
                    'filter_component' => 'rating',
                    'order' => 3,
                    'is_collapsed' => true,
                    'show_count' => false,
                ],
                [
                    'category_group_id' => $categoryGroupId,
                    'filter_type' => 'shipping',
                    'display_label' => 'Kargo',
                    'filter_component' => 'checkbox',
                    'order' => 4,
                    'is_collapsed' => false,
                    'show_count' => false,
                    'config' => [
                        'options' => [
                            ['value' => 'free_shipping', 'label' => 'Ücretsiz Kargo'],
                            ['value' => 'fast_delivery', 'label' => 'Hızlı Teslimat'],
                        ],
                    ],
                ],
            ];
        };

        // Tüm filtreleri birleştir
        $allFilters = array_merge(
            $giyimFilters,
            $elektronikFilters,
            $kozmetikFilters,
            $defaultFilters(4, 50000),  // Ev & Yaşam
            $defaultFilters(5, 10000),  // Spor
            $defaultFilters(6, 5000),   // Ayakkabı & Çanta
            $defaultFilters(7, 3000),   // Anne & Çocuk
            $defaultFilters(8, 2000),   // Süpermarket
        );

        // Kaydet
        foreach ($allFilters as $filter) {
            FilterConfig::updateOrCreate(
                [
                    'category_group_id' => $filter['category_group_id'],
                    'filter_type' => $filter['filter_type'],
                    'attribute_id' => $filter['attribute_id'] ?? null,
                ],
                array_merge($filter, ['is_active' => true])
            );
        }
    }
}
