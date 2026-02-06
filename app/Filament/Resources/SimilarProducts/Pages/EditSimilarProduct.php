<?php

namespace App\Filament\Resources\SimilarProducts\Pages;

use App\Filament\Resources\SimilarProducts\SimilarProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSimilarProduct extends EditRecord
{
    protected static string $resource = SimilarProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
