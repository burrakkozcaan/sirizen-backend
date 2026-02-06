<?php

namespace App\Filament\Resources\SearchIndices\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SearchIndexForm
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

                        Textarea::make('searchable_text')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
