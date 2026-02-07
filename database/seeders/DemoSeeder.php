<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Demo/test data for development and staging environments.
 * Do NOT run in production.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ========================================
            // VENDOR SİSTEMİ
            // ========================================
            VendorSeeder::class,
            VendorScoreSeeder::class,
            VendorBalanceSeeder::class,
            VendorPenaltySeeder::class,
            BrandVendorSeeder::class,
            CargoIntegrationSeeder::class,
            VendorDocumentSeeder::class,
            ShippingRuleSeeder::class,
            ReturnPolicySeeder::class,

            // ========================================
            // ÜRÜN SİSTEMİ
            // ========================================
            ProductSeeder::class,
            ProductSellerSeeder::class,
            CampaignSeeder::class,
            ProductCampaignSeeder::class,
            ProductBannerSeeder::class,
            ProductBadgeSeeder::class,
            ProductFeatureSeeder::class,
            BadgeRuleSeeder::class,
            SocialProofRuleSeeder::class,
            ProductBundleSeeder::class,
            SimilarProductSeeder::class,
            ProductStatsSeeder::class,
            ProductFaqSeeder::class,

            // ========================================
            // KULLANICI VERİLERİ
            // ========================================
            AddressSeeder::class,
            NotificationSettingSeeder::class,
            VendorFollowerSeeder::class,
            ProductReviewSeeder::class,
            ProductQuestionSeeder::class,
            ProductAnswerSeeder::class,
            ReviewExtrasSeeder::class,

            // ========================================
            // SİPARİŞ SİSTEMİ
            // ========================================
            OrderSeeder::class,
            PaymentSeeder::class,
            InvoiceSeeder::class,
            CommissionSeeder::class,
            VendorPayoutSeeder::class,
            ShipmentSeeder::class,
            ShipmentEventSeeder::class,
            SellerReviewSeeder::class,
            RefundSeeder::class,
            ProductReturnSeeder::class,
            DisputeSeeder::class,

            // ========================================
            // KULLANICI ETKİLEŞİMLERİ
            // ========================================
            CartSeeder::class,
            FavoriteSeeder::class,
            WishlistSeeder::class,
            RecentlyViewedSeeder::class,

            // ========================================
            // KUPONLAR VE BİLDİRİMLER
            // ========================================
            CouponSeeder::class,
            AlertSeeder::class,
            NotificationSeeder::class,

            // ========================================
            // İSTATİSTİKLER VE İÇERİK
            // ========================================
            VendorStatsSeeder::class,
            LogSeeder::class,
            ContentSeeder::class,
            ContactConsentSeeder::class,
        ]);
    }
}
