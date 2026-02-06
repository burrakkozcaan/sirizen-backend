<?php

it('resolves the r2 disk configuration from environment variables', function () {
    expect(config('filesystems.disks.r2.driver'))->toBe('s3')
        ->and(config('filesystems.disks.r2.key'))->not->toBeNull()
        ->and(config('filesystems.disks.r2.secret'))->not->toBeNull()
        ->and(config('filesystems.disks.r2.bucket'))->not->toBeNull()
        ->and(config('filesystems.disks.r2.endpoint'))->not->toBeNull()
        ->and(config('filesystems.disks.r2.url'))->not->toBeNull();
});

it('forces local storage for livewire temporary uploads when missing', function () {
    expect(config('livewire.temporary_file_upload.disk'))->toBe('local');
});
