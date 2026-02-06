<?php

namespace App\Filament\Resources\UserConsents\Pages;

use App\Filament\Resources\UserConsents\UserConsentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserConsents extends ListRecords
{
    protected static string $resource = UserConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
