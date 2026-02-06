<?php

namespace App\Filament\Resources\PdpLayouts\Pages;

use App\Filament\Resources\PdpLayouts\PdpLayoutResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPdpLayout extends EditRecord
{
    protected static string $resource = PdpLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
