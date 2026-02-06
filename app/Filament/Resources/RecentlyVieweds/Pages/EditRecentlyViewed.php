<?php

namespace App\Filament\Resources\RecentlyVieweds\Pages;

use App\Filament\Resources\RecentlyVieweds\RecentlyViewedResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecentlyViewed extends EditRecord
{
    protected static string $resource = RecentlyViewedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
