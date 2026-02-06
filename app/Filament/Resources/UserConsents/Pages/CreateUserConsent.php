<?php

namespace App\Filament\Resources\UserConsents\Pages;

use App\Filament\Resources\UserConsents\UserConsentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserConsent extends CreateRecord
{
    protected static string $resource = UserConsentResource::class;
}
