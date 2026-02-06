<?php

namespace App\Filament\Resources\FilterConfigs\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FilterConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_group_id')
                    ->label('Kategori Grubu')
                    ->relationship('categoryGroup', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('filter_type')
                    ->label('Filtre Tipi')
                    ->required()
                    ->options([
                        'attribute' => 'Özellik (Attribute)',
                        'price' => 'Fiyat',
                        'brand' => 'Marka',
                        'rating' => 'Puan',
                        'seller' => 'Satıcı',
                        'campaign' => 'Kampanya',
                    ])
                    ->live(),

                Select::make('attribute_id')
                    ->label('Özellik')
                    ->relationship('attribute', 'label')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('filter_type') === 'attribute'),

                TextInput::make('display_label')
                    ->label('Görünen Ad')
                    ->required()
                    ->placeholder('Beden, Renk, Fiyat Aralığı'),

                Select::make('filter_component')
                    ->label('Filtre Bileşeni')
                    ->options([
                        'checkbox' => 'Checkbox',
                        'range' => 'Aralık (Range)',
                        'select' => 'Seçim (Select)',
                        'multiselect' => 'Çoklu Seçim',
                        'color' => 'Renk Seçici',
                    ])
                    ->default('checkbox'),

                TextInput::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_collapsed')
                    ->label('Daraltılmış Göster')
                    ->helperText('Varsayılan olarak daraltılmış mı gösterilsin?')
                    ->default(false),

                Toggle::make('show_count')
                    ->label('Sayı Göster')
                    ->helperText('Her seçenek yanında ürün sayısı gösterilsin mi?')
                    ->default(true),

                KeyValue::make('config')
                    ->label('Ek Ayarlar')
                    ->keyLabel('Anahtar')
                    ->valueLabel('Değer')
                    ->helperText('Örn: min, max, step değerleri için'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
