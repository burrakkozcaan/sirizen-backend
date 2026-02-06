<?php

namespace App\Filament\Resources\ProductLiveStats\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductLiveStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'title')
                            ->required(),
                        TextInput::make('view_count')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('cart_count')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('purchase_count')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('view_count_24h')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
