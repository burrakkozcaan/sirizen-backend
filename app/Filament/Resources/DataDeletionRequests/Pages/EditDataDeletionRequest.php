<?php

namespace App\Filament\Resources\DataDeletionRequests\Pages;

use App\Filament\Resources\DataDeletionRequests\DataDeletionRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDataDeletionRequest extends EditRecord
{
    protected static string $resource = DataDeletionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
