<?php

use App\Models\User;
use App\Models\Vendor;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users without a vendor are redirected to the application pending page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))->assertRedirect(route('vendor.application.pending'));
});

test('authenticated vendors can visit the dashboard', function () {
    $vendor = Vendor::factory()->create();

    $this->actingAs($vendor->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->has('stats')
            ->has('recent_orders')
            ->has('recent_shipments')
            ->has('commission')
        );
});
