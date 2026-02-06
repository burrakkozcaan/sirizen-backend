<?php

namespace App\Filament\Resources\DataDeletionRequests\Pages;

use App\Filament\Resources\DataDeletionRequests\DataDeletionRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataDeletionRequests extends ListRecords
{
    protected static string $resource = DataDeletionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
