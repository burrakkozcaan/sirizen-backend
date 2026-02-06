<?php

namespace App\Filament\Resources\ReviewImages\Pages;

use App\Filament\Resources\ReviewImages\ReviewImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviewImages extends ListRecords
{
    protected static string $resource = ReviewImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
