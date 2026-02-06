<?php

namespace App\Filament\Resources\ReturnImages\Pages;

use App\Filament\Resources\ReturnImages\ReturnImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturnImage extends EditRecord
{
    protected static string $resource = ReturnImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
