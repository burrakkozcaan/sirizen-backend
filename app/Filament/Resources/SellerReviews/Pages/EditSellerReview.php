<?php

namespace App\Filament\Resources\SellerReviews\Pages;

use App\Filament\Resources\SellerReviews\SellerReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSellerReview extends EditRecord
{
    protected static string $resource = SellerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
