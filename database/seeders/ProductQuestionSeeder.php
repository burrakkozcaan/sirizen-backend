<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductQuestionCategory;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class ProductQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', UserRole::CUSTOMER)->get();
        $products = Product::with('productSellers')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $questionsAnswers = [
            [
                'question' => 'Bu ürün hangi malzemeden yapılmış?',
                'answer' => 'Ürünümüz %100 pamuklu kumaştan üretilmiştir.',
                'answered_by_vendor' => true,
                'category_slug' => 'malzeme-ve-kumas',
            ],
            [
                'question' => 'Bedenler büyük mü geliyor?',
                'answer' => 'Ürünlerimiz normal kalıptır. Kendi bedeninizi seçebilirsiniz.',
                'answered_by_vendor' => true,
                'category_slug' => 'beden-ve-kalip',
            ],
            [
                'question' => 'Kargo ücreti ne kadar?',
                'answer' => '150 TL ve üzeri alışverişlerde kargo ücretsizdir.',
                'answered_by_vendor' => true,
                'category_slug' => 'kargo-ve-teslimat',
            ],
            [
                'question' => 'İade süresi kaç gündür?',
                'answer' => 'Ürünü teslim aldıktan sonra 14 gün içinde ücretsiz iade edebilirsiniz.',
                'answered_by_vendor' => true,
                'category_slug' => 'iade-ve-degisim',
            ],
            [
                'question' => 'Yıkama talimatları nedir?',
                'answer' => '30 derecede yıkanabilir, ütü yapılabilir. Çamaşır makinasında yıkayabilirsiniz.',
                'answered_by_vendor' => true,
                'category_slug' => 'kullanim-ve-bakim',
            ],
            [
                'question' => 'Stokta var mı?',
                'answer' => 'Evet, seçtiğiniz beden stoklarımızda mevcuttur.',
                'answered_by_vendor' => true,
                'category_slug' => 'urun-ozellikleri',
            ],
        ];

        foreach ($products as $product) {
            $questionCount = rand(1, 3);

            for ($i = 0; $i < $questionCount; $i++) {
                if (! isset($customers[$i]) || ! isset($questionsAnswers[$i])) {
                    break;
                }

                $qa = $questionsAnswers[$i];

                // Find the category by slug
                $category = ProductQuestionCategory::where('slug', $qa['category_slug'])->first();

                ProductQuestion::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id' => $customers[$i]->id,
                        'question' => $qa['question'],
                    ],
                    [
                        'product_question_category_id' => $category?->id,
                        'vendor_id' => $product->vendor_id
                            ?? $product->productSellers->first()?->vendor_id,
                        'answer' => $qa['answer'],
                        'answered_by_vendor' => $qa['answered_by_vendor'],
                        'created_at' => now()->subDays(rand(5, 20)),
                    ]
                );
            }
        }
    }
}
