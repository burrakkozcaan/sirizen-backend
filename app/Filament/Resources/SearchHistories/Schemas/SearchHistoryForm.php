<?php

namespace App\Filament\Resources\SearchHistories\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SearchHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),

                        TextInput::make('query')
                            ->label('Arama Terimi')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('results_count')
                            ->label('Sonuç Sayısı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        DateTimePicker::make('searched_at')
                            ->label('Arama Tarihi'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
