<?php

namespace Database\Seeders;

use App\Models\ProductQuestion;
use Illuminate\Database\Seeder;

class ProductAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $questions = ProductQuestion::whereNull('answer')->get();

        $answers = [
            'Evet, bu ürün orijinaldir ve garantilidir.',
            'Ürün açıklamasında belirtilen tüm özellikler mevcuttur.',
            'Kargo 1-3 iş günü içinde teslim edilmektedir.',
            'Ürünün iade süresi 14 gündür.',
            'Farklı renk seçenekleri için lütfen varyantları kontrol edin.',
            'Bu ürün şu anda stoklarımızda mevcuttur.',
            'Bedeni normal kalıptır, normalden yarım beden büyük almanızı öneririz.',
            'Ürün faturalı olarak gönderilmektedir.',
            'Aynı gün kargo seçeneğimiz mevcuttur.',
            'Ürünün garanti süresi 2 yıldır.',
        ];

        foreach ($questions as $question) {
            // %70 ihtimalle cevapla
            if (fake()->boolean(70)) {
                $question->update([
                    'answer' => fake()->randomElement($answers),
                    'answered_by_vendor' => true,
                ]);
            }
        }
    }
}
