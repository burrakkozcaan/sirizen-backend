<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Static/reference data required for the application to function.
 * Safe to run in production: php artisan db:seed --class=CoreDataSeeder --force
 */
class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Users (admin accounts)
            UserSeeder::class,

            // Shipping companies
            ShippingCompanySeeder::class,

            // Category groups and categories
            CategoryGroupSeeder::class,
            CategorySeeder::class,

            // Brands
            BrandSeeder::class,

            // Cities and districts
            CitySeeder::class,

            // Attribute sets and attributes
            AttributeSetSeeder::class,
            AttributeSeeder::class,

            // Filter configurations
            FilterConfigSeeder::class,

            // Vendor tiers
            VendorTierSeeder::class,

            // Badge definitions and seller badges
            BadgeDefinitionSeeder::class,
            SellerBadgeSeeder::class,

            // Product question categories
            ProductQuestionCategorySeeder::class,

            // Membership programs
            MembershipProgramSeeder::class,

            // Quick links
            QuickLinkSeeder::class,
        ]);
    }
}
