<?php

use App\Models\Notification;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

test('notification defines expected relationships', function () {
    $notification = new Notification();

    expect($notification->user())->toBeInstanceOf(BelongsTo::class)
        ->and($notification->user()->getRelated())->toBeInstanceOf(User::class);
    expect($notification->order())->toBeInstanceOf(BelongsTo::class)
        ->and($notification->order()->getRelated())->toBeInstanceOf(Order::class);
    expect($notification->shipment())->toBeInstanceOf(BelongsTo::class)
        ->and($notification->shipment()->getRelated())->toBeInstanceOf(Shipment::class);
});
