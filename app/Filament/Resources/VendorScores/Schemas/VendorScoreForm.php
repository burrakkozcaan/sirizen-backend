<?php

namespace App\Filament\Resources\VendorScores\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('total_score')
                            ->numeric()
                            ->default(0),

                        TextInput::make('delivery_score')
                            ->numeric()
                            ->default(0),

                        TextInput::make('rating_score')
                            ->numeric()
                            ->default(0),

                        TextInput::make('stock_score')
                            ->numeric()
                            ->default(0),

                        TextInput::make('support_score')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
