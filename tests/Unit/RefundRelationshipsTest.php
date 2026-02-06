<?php

use App\Models\Refund;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

test('refund defines expected relationships', function () {
    $refund = new Refund();

    expect($refund->orderItem())->toBeInstanceOf(BelongsTo::class);
    expect($refund->user())->toBeInstanceOf(BelongsTo::class);
    expect($refund->vendor())->toBeInstanceOf(BelongsTo::class);
});
