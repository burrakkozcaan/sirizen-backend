<?php

use App\Models\OrderItem;
use App\ReturnReason;
use Illuminate\Support\Facades\DB;

it('normalizes legacy product return reasons', function () {
    $orderItem = OrderItem::factory()->create();

    $sizeIssueId = DB::table('product_returns')->insertGetId([
        'order_item_id' => $orderItem->id,
        'reason' => 'size_issue',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $wrongItemId = DB::table('product_returns')->insertGetId([
        'order_item_id' => $orderItem->id,
        'reason' => 'wrong_item',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = require base_path('database/migrations/2026_01_21_134406_normalize_product_return_reasons.php');
    $migration->up();

    expect(DB::table('product_returns')->where('id', $sizeIssueId)->value('reason'))
        ->toBe(ReturnReason::WRONG_SIZE->value);

    expect(DB::table('product_returns')->where('id', $wrongItemId)->value('reason'))
        ->toBe(ReturnReason::WRONG_PRODUCT->value);
});
