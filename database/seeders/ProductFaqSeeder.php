<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFaq;
use Illuminate\Database\Seeder;

class ProductFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $categories = Category::whereNull('parent_id')->get();

        if ($products->isEmpty() && $categories->isEmpty()) {
            return;
        }

        $productFaqs = [
            [
                'question' => 'Ürünün kargo süresi nedir?',
                'answer' => 'Siparişleriniz 1-3 iş günü içinde kargoya verilir.',
            ],
            [
                'question' => 'Bu ürün iade edilebilir mi?',
                'answer' => 'Ürün tesliminden itibaren 14 gün içinde iade edebilirsiniz.',
            ],
            [
                'question' => 'Garanti süresi ne kadar?',
                'answer' => 'Ürün 2 yıl üretici garantilidir.',
            ],
        ];

        foreach ($products as $index => $product) {
            $faq = $productFaqs[$index % count($productFaqs)];

            ProductFaq::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'question' => $faq['question'],
                ],
                [
                    'product_id' => $product->id,
                    'vendor_id' => $product->vendor_id,
                    'category_id' => $product->category_id,
                    'answer' => $faq['answer'],
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }

        $categoryFaqs = [
            [
                'question' => 'Bu kategoride iade koşulları nedir?',
                'answer' => 'Kategoriye özel iade koşulları için ürün sayfasındaki bilgileri kontrol edebilirsiniz.',
            ],
            [
                'question' => 'Bu kategori ürünleri hangi kargo ile gönderilir?',
                'answer' => 'Kargo şirketi satıcıya göre değişiklik gösterebilir.',
            ],
        ];

        foreach ($categories as $index => $category) {
            $faq = $categoryFaqs[$index % count($categoryFaqs)];

            ProductFaq::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'question' => $faq['question'],
                ],
                [
                    'category_id' => $category->id,
                    'answer' => $faq['answer'],
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}
