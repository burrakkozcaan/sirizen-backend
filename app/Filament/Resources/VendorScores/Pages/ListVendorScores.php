<?php

namespace App\Filament\Resources\VendorScores\Pages;

use App\Filament\Resources\VendorScores\VendorScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorScores extends ListRecords
{
    protected static string $resource = VendorScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
