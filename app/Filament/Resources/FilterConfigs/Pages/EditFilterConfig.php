<?php

namespace App\Filament\Resources\FilterConfigs\Pages;

use App\Filament\Resources\FilterConfigs\FilterConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFilterConfig extends EditRecord
{
    protected static string $resource = FilterConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
