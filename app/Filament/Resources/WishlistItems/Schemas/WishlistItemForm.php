<?php

namespace App\Filament\Resources\WishlistItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WishlistItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('wishlist_id')
                            ->label('İstek Listesi')
                            ->relationship('wishlist', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('product_id')
                            ->label('Ürün')
                            ->relationship('product', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
