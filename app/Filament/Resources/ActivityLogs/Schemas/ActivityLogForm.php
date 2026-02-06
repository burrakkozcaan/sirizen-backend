<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('user_id')
                            ->numeric(),

                        TextInput::make('action')
                            ->required(),

                        TextInput::make('ip_address'),

                        Textarea::make('user_agent')
                            ->columnSpanFull(),

                        Textarea::make('properties')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
