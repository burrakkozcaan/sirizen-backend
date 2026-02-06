<?php

namespace App\Filament\Resources\SimilarProducts\Pages;

use App\Filament\Resources\SimilarProducts\SimilarProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSimilarProducts extends ListRecords
{
    protected static string $resource = SimilarProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
