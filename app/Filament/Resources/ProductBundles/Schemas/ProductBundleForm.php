<?php

namespace App\Filament\Resources\ProductBundles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductBundleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('main_product_id')
                            ->relationship('mainProduct', 'title')
                            ->required(),
                        TextInput::make('title')
                            ->required(),
                        Select::make('bundle_type')
                            ->label('Paket Tipi')
                            ->options([
                                'quantity_discount' => 'Çok Al Az Öde',
                                'set' => 'Set Ürün',
                                'combo' => 'Kombinasyon',
                            ])
                            ->required()
                            ->default('quantity_discount'),
                        TextInput::make('discount_rate')
                            ->label('İndirim Oranı (%)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Örn: 10 = %10 indirim'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Section::make('Paket Ürünleri')
                    ->description('Bu pakete dahil edilecek ürünleri seçin (çoklu seçim)')
                    ->schema([
                        Select::make('products')
                            ->label('Ürünler')
                            ->relationship('products', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Bu pakete dahil edilecek ürünleri seçin'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
