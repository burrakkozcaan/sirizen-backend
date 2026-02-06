<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = User::where('role', 'admin')->get();

        // Hero Slides
        $heroSlides = [
            [
                'title' => 'Yaz Koleksiyonu',
                'subtitle' => 'Yeni sezon ürünleri keşfedin',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920',
                'link' => '/kategori/yaz-koleksiyonu',
                'button_text' => 'Alışverişe Başla',
            ],
            [
                'title' => 'Büyük İndirim',
                'subtitle' => 'Seçili ürünlerde %50\'ye varan indirim',
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920',
                'link' => '/kampanyalar/buyuk-indirim',
                'button_text' => 'İndirimleri Gör',
            ],
            [
                'title' => 'Ücretsiz Kargo',
                'subtitle' => '150 TL ve üzeri alışverişlerde',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920',
                'link' => '/kampanyalar',
                'button_text' => 'Detaylar',
            ],
        ];

        foreach ($heroSlides as $index => $slide) {
            DB::table('hero_slides')->insert([
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Static Pages
        $staticPages = [
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '<h2>Sirizen Hakkında</h2><p>Sirizen, Türkiye\'nin önde gelen e-ticaret platformlarından biridir. 2020 yılında kurulan şirketimiz, müşterilerine en kaliteli ürünleri en uygun fiyatlarla sunmayı hedeflemektedir.</p><h3>Misyonumuz</h3><p>Müşterilerimize güvenilir, hızlı ve kaliteli alışveriş deneyimi sunmak.</p><h3>Vizyonumuz</h3><p>Türkiye\'nin en güvenilir ve tercih edilen e-ticaret platformu olmak.</p>',
                'meta_title' => 'Hakkımızda | Sirizen',
                'meta_description' => 'Sirizen hakkında bilgi edinin. Misyonumuz, vizyonumuz ve değerlerimiz.',
            ],
            [
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => '<h2>Bize Ulaşın</h2><p><strong>Adres:</strong> İstanbul, Türkiye</p><p><strong>Telefon:</strong> 0850 123 45 67</p><p><strong>E-posta:</strong> info@sirizen.com</p><h3>Çalışma Saatleri</h3><p>Pazartesi - Cuma: 09:00 - 18:00</p><p>Cumartesi: 09:00 - 14:00</p>',
                'meta_title' => 'İletişim | Sirizen',
                'meta_description' => 'Sirizen iletişim bilgileri. Bize ulaşın.',
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '<h2>Gizlilik Politikası</h2><p>Bu gizlilik politikası, Sirizen\'in kişisel verilerinizi nasıl topladığını, kullandığını ve koruduğunu açıklar.</p><h3>Toplanan Veriler</h3><p>Ad, soyad, e-posta, telefon, adres bilgileri ve ödeme bilgileri toplanmaktadır.</p><h3>Verilerin Kullanımı</h3><p>Verileriniz siparişlerinizi işlemek, müşteri hizmetleri sağlamak ve pazarlama iletişimleri göndermek için kullanılır.</p>',
                'meta_title' => 'Gizlilik Politikası | Sirizen',
                'meta_description' => 'Sirizen gizlilik politikası. Kişisel verilerinizin korunması.',
            ],
            [
                'title' => 'Kullanım Koşulları',
                'slug' => 'kullanim-kosullari',
                'content' => '<h2>Kullanım Koşulları</h2><p>Bu web sitesini kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız.</p><h3>Genel Koşullar</h3><p>Site içeriği telif hakları ile korunmaktadır. İzinsiz kullanım yasaktır.</p><h3>Sipariş ve Ödeme</h3><p>Siparişler stok durumuna göre işleme alınır. Ödeme güvenliği sağlanmaktadır.</p>',
                'meta_title' => 'Kullanım Koşulları | Sirizen',
                'meta_description' => 'Sirizen kullanım koşulları ve şartları.',
            ],
            [
                'title' => 'İade ve Değişim',
                'slug' => 'iade-ve-degisim',
                'content' => '<h2>İade ve Değişim Politikası</h2><h3>İade Koşulları</h3><p>Ürünler, teslim tarihinden itibaren 14 gün içinde iade edilebilir.</p><h3>Değişim</h3><p>Beden değişikliği için 7 gün içinde başvuru yapılmalıdır.</p><h3>İade Süreci</h3><p>İade talebinizi hesabınızdan oluşturabilirsiniz. Onay sonrası kargo ile gönderim yapabilirsiniz.</p>',
                'meta_title' => 'İade ve Değişim | Sirizen',
                'meta_description' => 'Sirizen iade ve değişim politikası.',
            ],
        ];

        foreach ($staticPages as $page) {
            DB::table('static_pages')->insert([
                'title' => $page['title'],
                'slug' => $page['slug'],
                'content' => $page['content'],
                'is_active' => true,
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'created_at' => now()->subDays(rand(30, 90)),
                'updated_at' => now(),
            ]);
        }

        // Blog Posts
        $blogPosts = [
            [
                'title' => '2024 Yaz Modası Trendleri',
                'excerpt' => 'Bu yaz en çok tercih edilecek moda trendlerini keşfedin.',
                'content' => '<p>2024 yazında moda dünyasında heyecan verici trendler bizi bekliyor. Pastel tonlar, oversize kesimler ve doğal kumaşlar bu sezonun öne çıkan unsurları arasında yer alıyor.</p><h3>Renk Trendleri</h3><p>Lavanta, mint yeşili ve soft pembe bu yılın favori renkleri...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800',
            ],
            [
                'title' => 'Kapsül Gardırop Nasıl Oluşturulur?',
                'excerpt' => 'Minimalist bir gardıropla maksimum stil yaratmanın yolları.',
                'content' => '<p>Kapsül gardırop, az parçayla çok kombinasyon yapabilmenizi sağlayan akıllı bir sistem. İşte adım adım kapsül gardırop oluşturma rehberi...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
            ],
            [
                'title' => 'Sürdürülebilir Moda: Bilinçli Alışveriş',
                'excerpt' => 'Çevreye duyarlı moda seçimleri yapmanın önemi.',
                'content' => '<p>Sürdürülebilir moda, hem çevreyi korumak hem de etik üretimi desteklemek için önemli bir adım. Bilinçli tüketici olmanın yollarını keşfedin...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1532453288672-3a27e9be9efd?w=800',
            ],
        ];

        $author = $adminUsers->first();

        foreach ($blogPosts as $post) {
            $publishedAt = fake()->dateTimeBetween('-30 days', 'now');

            DB::table('blog_posts')->insert([
                'user_id' => $author?->id,
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'cover_image' => $post['cover_image'],
                'is_published' => true,
                'published_at' => $publishedAt,
                'meta_title' => $post['title'] . ' | Sirizen Blog',
                'meta_description' => $post['excerpt'],
                'created_at' => $publishedAt,
                'updated_at' => now(),
            ]);
        }
    }
}
