<?php

namespace App\Filament\Resources\BadgeDefinitions\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BadgeDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('fast_delivery, advantage, best_seller')
                    ->helperText('Sistemde benzersiz olmalıdır.'),

                TextInput::make('label')
                    ->label('Varsayılan Etiket')
                    ->required()
                    ->placeholder('Hızlı Teslimat, Avantajlı Ürün'),

                TextInput::make('icon')
                    ->label('İkon')
                    ->placeholder('heroicon-o-truck'),

                ColorPicker::make('color')
                    ->label('Varsayılan Renk'),

                ColorPicker::make('bg_color')
                    ->label('Arka Plan Rengi'),

                ColorPicker::make('text_color')
                    ->label('Yazı Rengi'),

                TextInput::make('priority')
                    ->label('Öncelik')
                    ->numeric()
                    ->default(0)
                    ->helperText('Yüksek değer önce gösterilir.'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Repeater::make('translations')
                    ->label('Kategori Grubu Çevirileri')
                    ->relationship('translations')
                    ->schema([
                        Select::make('category_group_id')
                            ->label('Kategori Grubu')
                            ->relationship('categoryGroup', 'name')
                            ->required(),

                        TextInput::make('label')
                            ->label('Etiket')
                            ->required(),

                        TextInput::make('icon')
                            ->label('İkon'),

                        ColorPicker::make('color')
                            ->label('Renk'),

                        ColorPicker::make('bg_color')
                            ->label('Arka Plan Rengi'),

                        ColorPicker::make('text_color')
                            ->label('Yazı Rengi'),
                    ])
                    ->columns(2),
            ]);
    }
}
