<?php

namespace App\Filament\Resources\SearchIndices\Pages;

use App\Filament\Resources\SearchIndices\SearchIndexResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSearchIndex extends EditRecord
{
    protected static string $resource = SearchIndexResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
