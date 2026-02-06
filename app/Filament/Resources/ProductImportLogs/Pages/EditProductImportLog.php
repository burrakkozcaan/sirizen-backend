<?php

namespace App\Filament\Resources\ProductImportLogs\Pages;

use App\Filament\Resources\ProductImportLogs\ProductImportLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductImportLog extends EditRecord
{
    protected static string $resource = ProductImportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
