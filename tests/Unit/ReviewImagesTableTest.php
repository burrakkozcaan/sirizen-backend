<?php

it('uses image path with a fallback for review images table', function () {
    $path = dirname(__DIR__, 2).'/app/Filament/Resources/ReviewImages/Tables/ReviewImagesTable.php';
    $contents = file_get_contents($path);

    expect($contents)
        ->toContain("ImageColumn::make('image_path')")
        ->toContain("->defaultImageUrl('/images/placeholder-brand.png')");
});
