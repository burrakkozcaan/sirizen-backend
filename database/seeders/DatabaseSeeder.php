<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // ========================================
            // 1. TEMEL VERİLER
            // ========================================

            // Users and authentication
            UserSeeder::class,

            // Shipping companies
            ShippingCompanySeeder::class,

            // Category groups and attributes (must be before categories)
            CategoryGroupSeeder::class,

            // Product related base data
            CategorySeeder::class,
            BrandSeeder::class,
            CitySeeder::class,

            // Attribute sets and attributes
            AttributeSetSeeder::class,
            AttributeSeeder::class,

            // Filter configurations (after attributes)
            FilterConfigSeeder::class,

            // ========================================
            // 2. VENDOR SİSTEMİ
            // ========================================

            // Vendor tiers (must be before vendors)
            VendorTierSeeder::class,

            // Vendors (depends on users and tiers)
            VendorSeeder::class,

            // Vendor scores, balances, penalties
            VendorScoreSeeder::class,
            VendorBalanceSeeder::class,
            VendorPenaltySeeder::class,

            // Vendor badges and assignments
            SellerBadgeSeeder::class,

            // Brand-vendor relationships (vendor-owned brands + authorizations)
            BrandVendorSeeder::class,

            // Cargo integrations (depends on vendors and shipping companies)
            CargoIntegrationSeeder::class,

            // Vendor documents (KYC documents)
            VendorDocumentSeeder::class,
            ShippingRuleSeeder::class,
            ReturnPolicySeeder::class,

            // ========================================
            // 3. ÜRÜN SİSTEMİ
            // ========================================

            // Products (depends on categories, brands, vendors)
            ProductSeeder::class,

            // Product-Seller relationships (depends on products and vendors)
            ProductSellerSeeder::class,

            // Campaigns (depends on products)
            CampaignSeeder::class,
            ProductCampaignSeeder::class,

            // Product features and badges
            ProductBannerSeeder::class,
            BadgeDefinitionSeeder::class, // Badge definitions (before badge rules)
            ProductBadgeSeeder::class,
            ProductFeatureSeeder::class,

            // Badge rules and social proof rules (after badges)
            BadgeRuleSeeder::class,
            SocialProofRuleSeeder::class,

            // Product bundles and similar products (after products)
            ProductBundleSeeder::class,
            SimilarProductSeeder::class,

            // Product stats and approvals
            ProductStatsSeeder::class,
            ProductFaqSeeder::class,

            // ========================================
            // 4. KULLANICI VERİLERİ
            // ========================================

            // User related data (depends on users)
            AddressSeeder::class,
            NotificationSettingSeeder::class,

            // Vendor followers (depends on users and vendors)
            VendorFollowerSeeder::class,

            // Question categories (depends on categories)
            ProductQuestionCategorySeeder::class,

            // Reviews and Questions (depends on users and products)
            ProductReviewSeeder::class,
            ProductQuestionSeeder::class,
            ProductAnswerSeeder::class,

            // Review extras (images, helpful votes)
            ReviewExtrasSeeder::class,

            // ========================================
            // 5. SİPARİŞ SİSTEMİ
            // ========================================

            // Orders (depends on users, addresses, product_sellers)
            OrderSeeder::class,

            // Payments (depends on orders)
            PaymentSeeder::class,

            // Invoices (depends on orders)
            InvoiceSeeder::class,

            // Commissions (depends on order items)
            CommissionSeeder::class,

            // Vendor payouts (depends on vendors)
            VendorPayoutSeeder::class,

            // Shipments (depends on orders)
            ShipmentSeeder::class,
            ShipmentEventSeeder::class,

            // Seller reviews (depends on delivered orders)
            SellerReviewSeeder::class,

            // Refunds (depends on cancelled orders)
            RefundSeeder::class,

            // Product returns (depends on delivered orders)
            ProductReturnSeeder::class,

            // Disputes (depends on orders)
            DisputeSeeder::class,

            // ========================================
            // 6. KULLANICI ETKİLEŞİMLERİ
            // ========================================

            // Carts (depends on users and product_sellers)
            CartSeeder::class,

            // Favorites (depends on users and products)
            FavoriteSeeder::class,

            // Wishlists (depends on users and products)
            WishlistSeeder::class,

            // Recently viewed (depends on users and products)
            RecentlyViewedSeeder::class,

            // ========================================
            // 7. KUPONLAR VE BİLDİRİMLER
            // ========================================

            // Coupons (depends on vendors, products, orders)
            CouponSeeder::class,

            // Alerts (depends on users and products)
            AlertSeeder::class,

            // Notifications (depends on users and orders)
            NotificationSeeder::class,

            // ========================================
            // 8. İSTATİSTİKLER VE LOGLAR
            // ========================================

            // Vendor stats (daily, analytics, SLA)
            VendorStatsSeeder::class,

            // Search and activity logs
            LogSeeder::class,

            // ========================================
            // 9. İÇERİK YÖNETİMİ
            // ========================================

            // Hero slides, static pages, blog posts
            MembershipProgramSeeder::class,
            QuickLinkSeeder::class,
            ContentSeeder::class,

            // Contact messages, user consents
            ContactConsentSeeder::class,
        ]);
    }
}
