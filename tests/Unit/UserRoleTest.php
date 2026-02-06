<?php

use App\Models\User;
use App\UserRole;

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

test('customer users are detected by the role helper', function () {
    $user = User::factory()->create([
        'role' => UserRole::CUSTOMER,
    ]);

    expect($user->isUser())->toBeTrue();
});
