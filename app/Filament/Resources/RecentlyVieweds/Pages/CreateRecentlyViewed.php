<?php

namespace App\Filament\Resources\RecentlyVieweds\Pages;

use App\Filament\Resources\RecentlyVieweds\RecentlyViewedResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecentlyViewed extends CreateRecord
{
    protected static string $resource = RecentlyViewedResource::class;
}
