<?php

namespace App\Filament\Resources\SearchHistories\Pages;

use App\Filament\Resources\SearchHistories\SearchHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSearchHistories extends ListRecords
{
    protected static string $resource = SearchHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
