<?php

namespace Database\Seeders;

use App\Models\SearchTag;
use Illuminate\Database\Seeder;

class SearchTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['label' => 'Moda', 'url' => '/search?q=Moda', 'order' => 1],
            ['label' => 'Erkek Modelleri', 'url' => '/search?q=Erkek+Modelleri', 'order' => 2],
            ['label' => 'Çocuk Ürünleri', 'url' => '/search?q=%C3%87ocuk+%C3%9Cr%C3%BCnleri', 'order' => 3],
            ['label' => 'Ev & Yaşam', 'url' => '/search?q=Ev+%26+Ya%C5%9Fam', 'order' => 4],
            ['label' => 'Elektronik', 'url' => '/search?q=Elektronik', 'order' => 5],
            ['label' => 'Spor & Outdoor', 'url' => '/search?q=Spor+%26+Outdoor', 'order' => 6],
            ['label' => 'Kozmetik', 'url' => '/search?q=Kozmetik', 'order' => 7],
            ['label' => 'Ayakkabı', 'url' => '/search?q=Ayakkab%C4%B1', 'order' => 8],
            ['label' => 'Çanta', 'url' => '/search?q=%C3%87anta', 'order' => 9],
            ['label' => 'Saat', 'url' => '/search?q=Saat', 'order' => 10],
            ['label' => 'Gözlük', 'url' => '/search?q=G%C3%B6zl%C3%BCk', 'order' => 11],
            ['label' => 'Takı', 'url' => '/search?q=Tak%C4%B1', 'order' => 12],
            ['label' => 'İndirimli Ürünler', 'url' => '/search?q=%C4%B0ndirimli+%C3%9Cr%C3%BCnler', 'order' => 13],
            ['label' => 'Yeni Sezon', 'url' => '/search?q=Yeni+Sezon', 'order' => 14],
            ['label' => 'Kampanyalı Ürünler', 'url' => '/search?q=Kampanyal%C4%B1+%C3%9Cr%C3%BCnler', 'order' => 15],
            ['label' => 'Popüler Markalar', 'url' => '/search?q=Pop%C3%BCler+Markalar', 'order' => 16],
        ];

        foreach ($tags as $tag) {
            SearchTag::updateOrCreate(
                ['label' => $tag['label']],
                array_merge($tag, ['is_active' => true])
            );
        }
    }
}
