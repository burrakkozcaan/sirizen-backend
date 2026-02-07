<?php

namespace App\Filament\Resources\SearchTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SearchTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Arama etiketi bilgilerini doldurun.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Etiket')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('/search?q=Moda'),

                                TextInput::make('order')
                                    ->label('Sıra')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
