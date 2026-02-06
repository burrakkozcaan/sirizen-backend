<?php

namespace App\Filament\Resources\SearchIndices\Pages;

use App\Filament\Resources\SearchIndices\SearchIndexResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSearchIndices extends ListRecords
{
    protected static string $resource = SearchIndexResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
