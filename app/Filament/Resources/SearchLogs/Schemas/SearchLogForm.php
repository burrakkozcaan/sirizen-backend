<?php

namespace App\Filament\Resources\SearchLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SearchLogForm
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

                        TextInput::make('query')
                            ->required(),

                        TextInput::make('results_count')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
