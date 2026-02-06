<?php

namespace App\Models;

use App\CompanyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    /** @use HasFactory<\Database\Factories\VendorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tier_id',
        'name',
        'slug',
        'description',
        'category',
        'company_type',
        'tax_number',
        'business_license_number',
        'iban',
        'bank_name',
        'account_holder_name',
        'city',
        'district',
        'reference_code',
        'address',
        'status',
        'kyc_status',
        'kyc_verified_at',
        'kyc_notes',
        'kyc_verified_by',
        'application_status',
        'application_submitted_at',
        'application_reviewed_at',
        'application_reviewed_by',
        'rejection_reason',
        'rating',
        'total_orders',
        'followers',
        'response_time_avg',
        'cancel_rate',
        'return_rate',
        'late_shipment_rate',
    ];

    protected function casts(): array
    {
        return [
            'company_type' => CompanyType::class,
            'rating' => 'decimal:2',
            'cancel_rate' => 'decimal:2',
            'return_rate' => 'decimal:2',
            'late_shipment_rate' => 'decimal:2',
            'kyc_verified_at' => 'datetime',
            'application_submitted_at' => 'datetime',
            'application_reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(VendorTier::class, 'tier_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_vendor');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sellers')
            ->withPivot(['price', 'stock', 'dispatch_days', 'shipping_type', 'is_featured']);
    }

    public function sellerBadges(): BelongsToMany
    {
        return $this->belongsToMany(SellerBadge::class, 'seller_badge_assignments', 'vendor_id', 'badge_id')
            ->withTimestamps()
            ->withPivot(['assigned_at', 'expires_at']);
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

    public function sellerPages(): HasMany
    {
        return $this->hasMany(SellerPage::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function productQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function sellerReviews(): HasMany
    {
        return $this->hasMany(SellerReview::class);
    }

    public function vendorScores(): HasMany
    {
        return $this->hasMany(VendorScore::class);
    }

    public function vendorPenalties(): HasMany
    {
        return $this->hasMany(VendorPenalty::class);
    }

    public function vendorFollowers(): HasMany
    {
        return $this->hasMany(VendorFollower::class);
    }

    /**
     * Users who follow this vendor
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vendor_followers')
            ->withTimestamps();
    }

    public function shippingRules(): HasMany
    {
        return $this->hasMany(ShippingRule::class);
    }

    public function returnPolicies(): HasMany
    {
        return $this->hasMany(ReturnPolicy::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Brands owned by this vendor (vendor-created brands)
     */
    public function ownedBrands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Brands this vendor is authorized to sell
     */
    public function authorizedBrands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_vendor')
            ->withPivot([
                'is_authorized',
                'authorized_at',
                'authorization_type',
                'authorization_document',
                'invoice_document',
                'valid_from',
                'valid_until',
                'status',
            ])
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function kycVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kyc_verified_by');
    }

    public function applicationReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'application_reviewed_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function cargoIntegrations(): HasMany
    {
        return $this->hasMany(CargoIntegration::class);
    }

    public function productImportLogs(): HasMany
    {
        return $this->hasMany(ProductImportLog::class);
    }

    public function slaMetrics(): HasMany
    {
        return $this->hasMany(VendorSlaMetric::class);
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(VendorDailyStat::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(VendorAnalytic::class);
    }

    public function productApprovals(): HasMany
    {
        return $this->hasMany(ProductApproval::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }
}
