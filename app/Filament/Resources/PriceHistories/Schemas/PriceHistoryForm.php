<?php

namespace App\Filament\Resources\PriceHistories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PriceHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('variant_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->required(),

                        TextInput::make('sale_price')
                            ->numeric(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
