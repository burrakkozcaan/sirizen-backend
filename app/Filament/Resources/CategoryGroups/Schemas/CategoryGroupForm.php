<?php

namespace App\Filament\Resources\CategoryGroups\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('giyim, kozmetik, elektronik')
                    ->helperText('Sistemde benzersiz olmalıdır. Küçük harf ve tire kullanın.'),

                TextInput::make('name')
                    ->label('Ad')
                    ->required()
                    ->placeholder('Giyim, Kozmetik, Elektronik'),

                TextInput::make('icon')
                    ->label('İkon')
                    ->placeholder('heroicon-o-shopping-bag')
                    ->helperText('Heroicon veya benzeri bir ikon sınıfı'),

                ColorPicker::make('color')
                    ->label('Renk'),

                KeyValue::make('metadata')
                    ->label('Ek Ayarlar')
                    ->keyLabel('Anahtar')
                    ->valueLabel('Değer')
                    ->helperText('Kategori grubuna özel ek ayarlar'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
