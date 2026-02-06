<?php

namespace App\Filament\Resources\UserConsents\Pages;

use App\Filament\Resources\UserConsents\UserConsentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserConsent extends EditRecord
{
    protected static string $resource = UserConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
