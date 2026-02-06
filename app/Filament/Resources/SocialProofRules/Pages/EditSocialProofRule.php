<?php

namespace App\Filament\Resources\SocialProofRules\Pages;

use App\Filament\Resources\SocialProofRules\SocialProofRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialProofRule extends EditRecord
{
    protected static string $resource = SocialProofRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
