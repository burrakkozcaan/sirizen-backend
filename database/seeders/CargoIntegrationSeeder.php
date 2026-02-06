<?php

namespace Database\Seeders;

use App\Models\ShippingCompany;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        $shippingCompanies = ShippingCompany::all();
        $vendors = Vendor::where('status', 'active')->get();

        if ($shippingCompanies->isEmpty() || $vendors->isEmpty()) {
            return;
        }

        foreach ($vendors as $vendor) {
            // Her vendor 1-3 kargo şirketi ile entegre
            $companyCount = rand(1, min(3, $shippingCompanies->count()));
            $selectedCompanies = $shippingCompanies->random($companyCount);

            foreach ($selectedCompanies as $company) {
                DB::table('cargo_integrations')->insert([
                    'shipping_company_id' => $company->id,
                    'vendor_id' => $vendor->id,
                    'integration_type' => fake()->randomElement(['api', 'webhook', 'manual']),
                    'api_endpoint' => 'https://api.' . $company->slug . '.com/v1',
                    'api_key' => 'ak_' . fake()->regexify('[A-Za-z0-9]{32}'),
                    'api_secret' => 'sk_' . fake()->regexify('[A-Za-z0-9]{48}'),
                    'customer_code' => strtoupper(substr($vendor->slug, 0, 3)) . '-' . fake()->numerify('######'),
                    'api_credentials' => json_encode([
                        'username' => $vendor->slug . '_user',
                        'password' => fake()->password(12, 16),
                        'merchant_id' => fake()->numerify('MER#######'),
                    ]),
                    'configuration' => json_encode([
                        'auto_create_shipment' => fake()->boolean(70),
                        'default_service_type' => fake()->randomElement(['standard', 'express', 'economy']),
                        'webhook_enabled' => fake()->boolean(80),
                        'label_format' => fake()->randomElement(['PDF', 'ZPL', 'PNG']),
                    ]),
                    'is_active' => fake()->boolean(90),
                    'is_test_mode' => fake()->boolean(20),
                    'last_sync_at' => fake()->optional(0.7)->dateTimeBetween('-7 days', 'now'),
                    'last_error' => fake()->optional(0.1)->sentence(),
                    'created_at' => now()->subDays(rand(30, 90)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
