<?php

it('wraps resource schemas in layout components', function () {
    $basePath = dirname(__DIR__, 2);
    $resourcePath = $basePath.'/app/Filament/Resources';

    $schemaFiles = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($resourcePath));

    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }

        $path = $file->getPathname();

        if (! str_contains($path, '/Schemas/')) {
            continue;
        }

        if (! str_ends_with($path, 'Form.php')) {
            continue;
        }

        $schemaFiles[] = $path;
    }

    expect($schemaFiles)->not->toBeEmpty();

    foreach ($schemaFiles as $path) {
        $contents = file_get_contents($path);

        expect($contents)->toContain('Section::make(');
    }
});
