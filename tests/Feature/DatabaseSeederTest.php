<?php

use App\Models\QuickLink;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

it('seeds core data', function () {
    $this->seed(DatabaseSeeder::class);

    expect(QuickLink::query()->count())->toBeGreaterThan(0);
    expect(DB::table('blog_posts')->count())->toBeGreaterThan(0);
    expect(DB::table('cities')->count())->toBeGreaterThan(0);
    expect(DB::table('contact_messages')->count())->toBeGreaterThan(0);
    expect(DB::table('membership_programs')->count())->toBeGreaterThan(0);
    expect(DB::table('notification_settings')->count())->toBeGreaterThan(0);
    expect(DB::table('product_faqs')->count())->toBeGreaterThan(0);
    expect(DB::table('product_questions')->whereNotNull('vendor_id')->count())->toBeGreaterThan(0);
    expect(DB::table('review_images')->count())->toBeGreaterThan(0);
    expect(DB::table('return_policies')->count())->toBeGreaterThan(0);
    expect(DB::table('shipping_rules')->count())->toBeGreaterThan(0);
    expect(DB::table('shipment_events')->count())->toBeGreaterThan(0);
    expect(DB::table('vendor_sla_metrics')->max('customer_satisfaction_score'))->toBeLessThanOrEqual(5);
});
