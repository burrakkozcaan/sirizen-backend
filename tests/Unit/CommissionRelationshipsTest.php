<?php

use App\Models\Commission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

test('commission defines expected relationships', function () {
    $commission = new Commission();

    expect($commission->orderItem())->toBeInstanceOf(BelongsTo::class);
    expect($commission->vendor())->toBeInstanceOf(BelongsTo::class);
});
