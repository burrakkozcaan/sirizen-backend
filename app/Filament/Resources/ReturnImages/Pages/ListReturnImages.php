<?php

namespace App\Filament\Resources\ReturnImages\Pages;

use App\Filament\Resources\ReturnImages\ReturnImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReturnImages extends ListRecords
{
    protected static string $resource = ReturnImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
