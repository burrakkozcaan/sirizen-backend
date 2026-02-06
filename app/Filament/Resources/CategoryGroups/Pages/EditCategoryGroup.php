<?php

namespace App\Filament\Resources\CategoryGroups\Pages;

use App\Filament\Resources\CategoryGroups\CategoryGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryGroup extends EditRecord
{
    protected static string $resource = CategoryGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
