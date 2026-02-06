<?php

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

it('has a user relationship', function () {
    $setting = new NotificationSetting();

    expect($setting->user())->toBeInstanceOf(BelongsTo::class)
        ->and($setting->user()->getRelated())->toBeInstanceOf(User::class);
});
