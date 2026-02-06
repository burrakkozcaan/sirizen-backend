<?php

it('uses Filament TextSize enum for the seller badge icon column', function () {
    $path = dirname(__DIR__, 2).'/app/Filament/Resources/SellerBadges/Tables/SellerBadgesTable.php';
    $contents = file_get_contents($path);

    expect($contents)
        ->toContain('TextSize::Large')
        ->not->toContain('TextColumnSize::');
});
