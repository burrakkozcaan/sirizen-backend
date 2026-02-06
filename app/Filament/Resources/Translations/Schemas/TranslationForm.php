<?php

namespace App\Filament\Resources\Translations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('entity_type')
                            ->required(),

                        TextInput::make('entity_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('locale')
                            ->required(),

                        TextInput::make('field')
                            ->required(),

                        Textarea::make('value')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
