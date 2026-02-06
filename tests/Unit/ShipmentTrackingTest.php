<?php

use App\Models\Notification;
use App\Models\ShipmentEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('links shipment events to shipments', function () {
    $event = ShipmentEvent::factory()->create();

    expect($event->shipment)
        ->not->toBeNull()
        ->and($event->shipment->events()->whereKey($event->id)->exists())->toBeTrue();
});

it('casts notification data to an array', function () {
    $notification = Notification::factory()->create([
        'data' => [
            'status' => 'Yolda',
        ],
    ]);

    expect($notification->data)->toMatchArray([
        'status' => 'Yolda',
    ]);
});
