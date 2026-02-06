<?php

namespace App\Filament\Resources\PdpLayouts\Pages;

use App\Filament\Resources\PdpLayouts\PdpLayoutResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPdpLayouts extends ListRecords
{
    protected static string $resource = PdpLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
