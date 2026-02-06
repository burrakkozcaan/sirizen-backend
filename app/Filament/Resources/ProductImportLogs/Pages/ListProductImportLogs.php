<?php

namespace App\Filament\Resources\ProductImportLogs\Pages;

use App\Filament\Resources\ProductImportLogs\ProductImportLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductImportLogs extends ListRecords
{
    protected static string $resource = ProductImportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
