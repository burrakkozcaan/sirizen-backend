<?php

namespace App\Filament\Resources\SellerReviews\Pages;

use App\Filament\Resources\SellerReviews\SellerReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSellerReviews extends ListRecords
{
    protected static string $resource = SellerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
