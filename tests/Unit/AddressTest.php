<?php

use App\Models\Address;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

it('has a user relationship', function () {
    $address = new Address;

    expect($address->user())->toBeInstanceOf(BelongsTo::class)
        ->and($address->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($address->vendor())->toBeInstanceOf(BelongsTo::class)
        ->and($address->vendor()->getRelated())->toBeInstanceOf(Vendor::class);
});
