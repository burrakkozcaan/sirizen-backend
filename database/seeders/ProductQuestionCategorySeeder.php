<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductQuestionCategory;
use Illuminate\Database\Seeder;

class ProductQuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kargo ve Teslimat',
                'slug' => 'kargo-ve-teslimat',
                'icon' => '🚚',
                'order' => 1,
                'categories' => 'all', // Tüm kategoriler için
            ],
            [
                'name' => 'Ürün Özellikleri',
                'slug' => 'urun-ozellikleri',
                'icon' => '📋',
                'order' => 2,
                'categories' => 'all',
            ],
            [
                'name' => 'Beden ve Kalıp',
                'slug' => 'beden-ve-kalip',
                'icon' => '👔',
                'order' => 3,
                'categories' => ['Kadın', 'Erkek', 'Anne & Çocuk'], // Sadece giyim kategorileri
            ],
            [
                'name' => 'Malzeme ve Kumaş',
                'slug' => 'malzeme-ve-kumas',
                'icon' => '🧵',
                'order' => 4,
                'categories' => ['Kadın', 'Erkek', 'Anne & Çocuk'],
            ],
            [
                'name' => 'Renk ve Desen',
                'slug' => 'renk-ve-desen',
                'icon' => '🎨',
                'order' => 5,
                'categories' => 'all',
            ],
            [
                'name' => 'Kullanım ve Bakım',
                'slug' => 'kullanim-ve-bakim',
                'icon' => '🧼',
                'order' => 6,
                'categories' => 'all',
            ],
            [
                'name' => 'Fiyat ve İndirim',
                'slug' => 'fiyat-ve-indirim',
                'icon' => '💰',
                'order' => 7,
                'categories' => 'all',
            ],
            [
                'name' => 'İade ve Değişim',
                'slug' => 'iade-ve-degisim',
                'icon' => '🔄',
                'order' => 8,
                'categories' => 'all',
            ],
            [
                'name' => 'Garanti ve Sertifika',
                'slug' => 'garanti-ve-sertifika',
                'icon' => '✓',
                'order' => 9,
                'categories' => ['Elektronik', 'Ev & Yaşam'],
            ],
            [
                'name' => 'Taban Özellikleri',
                'slug' => 'taban-ozellikleri',
                'icon' => '👟',
                'order' => 10,
                'categories' => ['Ayakkabı & Çanta'],
            ],
            [
                'name' => 'Suya Dayanıklılık',
                'slug' => 'suya-dayaniklilik',
                'icon' => '💧',
                'order' => 11,
                'categories' => ['Ayakkabı & Çanta', 'Spor & Outdoor'],
            ],
            [
                'name' => 'Astar Özellikleri',
                'slug' => 'astar-ozellikleri',
                'icon' => '🧥',
                'order' => 12,
                'categories' => ['Kadın', 'Erkek', 'Anne & Çocuk', 'Ayakkabı & Çanta'],
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryNames = $categoryData['categories'];
            unset($categoryData['categories']);

            $questionCategory = ProductQuestionCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Kategorileri bağla (syncWithoutDetaching to avoid duplicates)
            if ($categoryNames === 'all') {
                // Tüm ana kategorilere bağla
                $allCategories = Category::whereNull('parent_id')->get();
                $questionCategory->categories()->syncWithoutDetaching($allCategories->pluck('id'));
            } else {
                // Belirli kategorilere bağla
                $selectedCategories = Category::whereIn('name', $categoryNames)
                    ->whereNull('parent_id')
                    ->get();
                $questionCategory->categories()->syncWithoutDetaching($selectedCategories->pluck('id'));
            }
        }
    }
}
