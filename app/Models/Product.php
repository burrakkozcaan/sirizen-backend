<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'category_id',
        'vendor_id',
        'title',
        'slug',
        'barcode',
        'model_code',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'description',
        'short_description',
        'tags',
        'additional_information',
        'safety_information',
        'additional_info',
        'safety_info',
        'manufacturer_name',
        'manufacturer_address',
        'manufacturer_contact',
        'responsible_party_name',
        'responsible_party_address',
        'responsible_party_contact',
        'rating',
        'reviews_count',
        'is_active',
        'is_bestseller',
        'is_new',
        'price',
        'discount_price',
        'original_price',
        'custom_commission_rate',
        'stock',
        'currency',
        'shipping_time',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'reviews_count' => 'integer',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'custom_commission_rate' => 'decimal:2',
            'stock' => 'integer',
            'shipping_time' => 'integer',
            'tags' => 'array',
        ];
    }

    /**
     * Get additional_info as array (for API)
     */
    public function getAdditionalInfoArrayAttribute()
    {
        $value = $this->attributes['additional_info'] ?? null;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return [];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productSellers(): HasMany
    {
        return $this->hasMany(ProductSeller::class);
    }

    /**
     * @deprecated Use productSellers() instead.
     */
    public function productVendors(): HasMany
    {
        return $this->productSellers();
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ProductVideo::class)->orderBy('order');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'product_campaigns');
    }

    public function productBanners(): HasMany
    {
        return $this->hasMany(ProductBanner::class);
    }

    public function productBadges(): HasMany
    {
        return $this->hasMany(ProductBadge::class);
    }

    public function productFeatures(): HasMany
    {
        return $this->hasMany(ProductFeature::class);
    }

    public function productCampaigns(): HasMany
    {
        return $this->hasMany(ProductCampaign::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function safetyImages(): HasMany
    {
        return $this->hasMany(ProductSafetyImage::class);
    }

    public function safetyDocuments(): HasMany
    {
        return $this->hasMany(ProductSafetyDocument::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function priceAlerts(): HasMany
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(ProductGuarantee::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class);
    }

    public function similarProducts(): HasMany
    {
        return $this->hasMany(SimilarProduct::class);
    }

    public function relatedProducts(): HasMany
    {
        return $this->hasMany(RelatedProduct::class);
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function attributeSet(): BelongsTo
    {
        return $this->belongsTo(AttributeSet::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function badgeSnapshots(): HasMany
    {
        return $this->hasMany(ProductBadgeSnapshot::class);
    }

    /**
     * İndirim yüzdesini hesapla
     */
    public function getDiscountPercentageAttribute(): float
    {
        if (! $this->discount_price || $this->discount_price >= $this->price) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
    }

    /**
     * Hızlı teslimat özelliği
     */
    public function getFastDeliveryAttribute(): bool
    {
        return $this->shipping_time !== null && $this->shipping_time <= 1;
    }
}
