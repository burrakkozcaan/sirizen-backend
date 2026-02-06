<?php

namespace App\Filament\Resources\ReviewImages\Pages;

use App\Filament\Resources\ReviewImages\ReviewImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReviewImage extends EditRecord
{
    protected static string $resource = ReviewImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
