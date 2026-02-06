<?php

it('uses product title for order items table', function () {
    $path = dirname(__DIR__, 2).'/app/Filament/Resources/OrderItems/Tables/OrderItemsTable.php';
    $contents = file_get_contents($path);

    expect($contents)
        ->toContain("TextColumn::make('product.title')")
        ->not->toContain("TextColumn::make('product.name')");
});
