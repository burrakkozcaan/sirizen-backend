<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdpBlock extends Model
{
    /** @use HasFactory<\Database\Factories\PdpBlockFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'component',
        'type',
        'default_props',
        'allowed_positions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_props' => 'array',
            'allowed_positions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Varsayılan PDP bloklarını seed et
     */
    public static function getDefaultBlocks(): array
    {
        return [
            ['key' => 'gallery', 'name' => 'Görsel Galeri', 'component' => 'ProductGallery', 'type' => 'static', 'allowed_positions' => ['main', 'sidebar']],
            ['key' => 'title', 'name' => 'Ürün Başlığı', 'component' => 'ProductTitle', 'type' => 'static', 'allowed_positions' => ['main']],
            ['key' => 'rating', 'name' => 'Değerlendirme', 'component' => 'ProductRating', 'type' => 'static', 'allowed_positions' => ['main']],
            ['key' => 'price', 'name' => 'Fiyat', 'component' => 'ProductPrice', 'type' => 'static', 'allowed_positions' => ['main', 'sidebar']],
            ['key' => 'badges', 'name' => 'Rozetler', 'component' => 'ProductBadges', 'type' => 'dynamic', 'allowed_positions' => ['main', 'under_title']],
            ['key' => 'social_proof', 'name' => 'Sosyal Kanıt', 'component' => 'SocialProof', 'type' => 'dynamic', 'allowed_positions' => ['main', 'under_title']],
            ['key' => 'variant_selector', 'name' => 'Varyant Seçici', 'component' => 'VariantSelector', 'type' => 'conditional', 'allowed_positions' => ['main']],
            ['key' => 'size_selector', 'name' => 'Beden Seçici', 'component' => 'SizeSelector', 'type' => 'conditional', 'allowed_positions' => ['main']],
            ['key' => 'attributes_highlight', 'name' => 'Öne Çıkan Özellikler', 'component' => 'HighlightAttributes', 'type' => 'dynamic', 'allowed_positions' => ['main']],
            ['key' => 'delivery_info', 'name' => 'Teslimat Bilgisi', 'component' => 'DeliveryInfo', 'type' => 'static', 'allowed_positions' => ['main', 'sidebar']],
            ['key' => 'campaigns', 'name' => 'Kampanyalar', 'component' => 'ProductCampaigns', 'type' => 'conditional', 'allowed_positions' => ['main']],
            ['key' => 'add_to_cart', 'name' => 'Sepete Ekle', 'component' => 'AddToCart', 'type' => 'static', 'allowed_positions' => ['main', 'sidebar']],
            ['key' => 'description', 'name' => 'Ürün Açıklaması', 'component' => 'ProductDescription', 'type' => 'static', 'allowed_positions' => ['main', 'bottom']],
            ['key' => 'attributes_detail', 'name' => 'Tüm Özellikler', 'component' => 'AttributesDetail', 'type' => 'static', 'allowed_positions' => ['main', 'bottom']],
            ['key' => 'reviews', 'name' => 'Değerlendirmeler', 'component' => 'ProductReviews', 'type' => 'static', 'allowed_positions' => ['main', 'bottom']],
            ['key' => 'questions', 'name' => 'Soru & Cevap', 'component' => 'ProductQuestions', 'type' => 'conditional', 'allowed_positions' => ['main', 'bottom']],
            ['key' => 'related_products', 'name' => 'Benzer Ürünler', 'component' => 'RelatedProducts', 'type' => 'dynamic', 'allowed_positions' => ['bottom']],
        ];
    }
}
