<?php

namespace App\Filament\Resources\CategoryGroups\Pages;

use App\Filament\Resources\CategoryGroups\CategoryGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryGroups extends ListRecords
{
    protected static string $resource = CategoryGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
