<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorStatsSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::where('status', 'active')->get();

        if ($vendors->isEmpty()) {
            return;
        }

        // Son 30 gün için günlük istatistikler
        foreach ($vendors as $vendor) {
            for ($day = 30; $day >= 1; $day--) {
                $date = now()->subDays($day)->format('Y-m-d');

                // Vendor Daily Stats
                $totalOrders = fake()->numberBetween(5, 50);
                $revenue = fake()->randomFloat(2, 1000, 20000);
                $commission = $revenue * fake()->randomFloat(2, 0.08, 0.15);

                DB::table('vendor_daily_stats')->insert([
                    'vendor_id' => $vendor->id,
                    'stat_date' => $date,
                    'total_sales' => $totalOrders, // Integer - toplam sipariş sayısı
                    'revenue' => $revenue,
                    'commission' => $commission,
                    'net_revenue' => $revenue - $commission,
                    'orders_count' => $totalOrders,
                    'products_sold' => $totalOrders * fake()->numberBetween(1, 3),
                    'new_customers' => fake()->numberBetween(2, 15),
                    'returning_customers' => fake()->numberBetween(1, 10),
                    'avg_order_value' => $revenue / max($totalOrders, 1),
                    'page_views' => fake()->numberBetween(500, 5000),
                    'product_views' => fake()->numberBetween(200, 2000),
                    'conversion_rate' => fake()->randomFloat(2, 1, 8),
                    'created_at' => $date,
                    'updated_at' => now(),
                ]);

                // Vendor Analytics (daha detaylı)
                DB::table('vendor_analytics')->insert([
                    'vendor_id' => $vendor->id,
                    'date' => $date,
                    'total_sales' => $revenue,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $revenue / max($totalOrders, 1),
                    'units_sold' => $totalOrders * fake()->numberBetween(1, 4),
                    'commission_amount' => $commission,
                    'net_earnings' => $revenue - $commission,
                    'pending_payout' => fake()->randomFloat(2, 0, 5000),
                    'active_products' => fake()->numberBetween(50, 200),
                    'out_of_stock_products' => fake()->numberBetween(0, 10),
                    'products_views' => fake()->numberBetween(200, 2000),
                    'conversion_rate' => fake()->randomFloat(2, 1, 8),
                    'unique_customers' => fake()->numberBetween(10, 40),
                    'new_customers' => fake()->numberBetween(2, 15),
                    'returning_customers' => fake()->numberBetween(5, 25),
                    'total_reviews' => fake()->numberBetween(0, 10),
                    'average_rating' => fake()->randomFloat(1, 3.5, 5),
                    'questions_answered' => fake()->numberBetween(0, 5),
                    'response_time_hours' => fake()->randomFloat(1, 0.5, 24),
                    'shipped_on_time' => $totalOrders - fake()->numberBetween(0, 3),
                    'late_shipments' => fake()->numberBetween(0, 3),
                    'cancelled_orders' => fake()->numberBetween(0, 2),
                    'returned_orders' => fake()->numberBetween(0, 2),
                    'created_at' => $date,
                    'updated_at' => now(),
                ]);

                // Vendor SLA Metrics (haftalık)
                if ($day % 7 === 0) {
                    $weeklyOrders = $totalOrders * 7;
                    $cancelledOrders = fake()->numberBetween(0, 5);
                    $returnedOrders = fake()->numberBetween(0, 3);
                    $lateShipments = fake()->numberBetween(0, 4);

                    DB::table('vendor_sla_metrics')->insert([
                        'vendor_id' => $vendor->id,
                        'metric_date' => $date,
                        'total_orders' => $weeklyOrders,
                        'cancelled_orders' => $cancelledOrders,
                        'returned_orders' => $returnedOrders,
                        'late_shipments' => $lateShipments,
                        'on_time_shipments' => $weeklyOrders - $lateShipments,
                        'cancel_rate' => min(9.99, round(($cancelledOrders / max($weeklyOrders, 1)) * 100, 2)),
                        'return_rate' => min(9.99, round(($returnedOrders / max($weeklyOrders, 1)) * 100, 2)),
                        'late_shipment_rate' => min(9.99, round(($lateShipments / max($weeklyOrders, 1)) * 100, 2)),
                        'avg_shipment_time' => fake()->numberBetween(12, 48), // Integer saat
                        'avg_response_time' => fake()->numberBetween(1, 12), // Integer saat
                        'total_questions_answered' => fake()->numberBetween(10, 50),
                        'total_reviews_responded' => fake()->numberBetween(5, 30),
                        'customer_satisfaction_score' => fake()->randomFloat(2, 3, 5),
                        'sla_violations' => json_encode(['count' => fake()->numberBetween(0, 3)]),
                        'created_at' => $date,
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
