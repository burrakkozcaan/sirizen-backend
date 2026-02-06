<?php

namespace App\Filament\Resources\UserConsents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserConsentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('consent_type')
                    ->required(),
                TextInput::make('consent_version')
                    ->required(),
                Toggle::make('is_granted')
                    ->required(),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
                DateTimePicker::make('granted_at'),
                DateTimePicker::make('revoked_at'),
            ]);
    }
}
