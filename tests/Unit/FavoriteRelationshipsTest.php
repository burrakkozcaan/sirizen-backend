<?php

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

test('favorite defines expected relationships', function () {
    $favorite = new Favorite();

    expect($favorite->user())->toBeInstanceOf(BelongsTo::class)
        ->and($favorite->user()->getRelated())->toBeInstanceOf(User::class);
    expect($favorite->product())->toBeInstanceOf(BelongsTo::class)
        ->and($favorite->product()->getRelated())->toBeInstanceOf(Product::class);
});
