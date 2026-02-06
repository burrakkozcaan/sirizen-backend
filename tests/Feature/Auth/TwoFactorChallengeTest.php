<?php

use App\Models\User;
use App\Models\Vendor;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;

test('two factor challenge redirects to login when not authenticated', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $response = $this->get(route('two-factor.login'));

    $response->assertRedirect(route('login'));
});

test('two factor challenge can be rendered', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()
        ->withTwoFactor()
        ->create();

    Vendor::factory()
        ->for($user)
        ->active()
        ->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->get(route('two-factor.login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/two-factor-challenge')
        );
});
