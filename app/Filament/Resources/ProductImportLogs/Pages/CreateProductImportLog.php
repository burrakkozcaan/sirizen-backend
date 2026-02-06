<?php

namespace App\Filament\Resources\ProductImportLogs\Pages;

use App\Filament\Resources\ProductImportLogs\ProductImportLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductImportLog extends CreateRecord
{
    protected static string $resource = ProductImportLogResource::class;
}
