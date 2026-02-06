<?php

namespace App\Filament\Resources\Carts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sepet Bilgileri')
                    ->description('Sepet bilgileri')
                    ->schema([
                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Section::make('Sepet Ürünleri')
                    ->description('Sepetteki ürünler')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Ürün')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Miktar')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                                TextInput::make('price')
                                    ->label('Birim Fiyat')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
