<?php

namespace App\Filament\Resources\VendorDocuments\Pages;

use App\Filament\Resources\VendorDocuments\VendorDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorDocument extends EditRecord
{
    protected static string $resource = VendorDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
