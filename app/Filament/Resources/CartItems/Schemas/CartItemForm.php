<?php

namespace App\Filament\Resources\CartItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CartItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('cart_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('product_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('product_seller_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1),

                        TextInput::make('price')
                            ->numeric()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
