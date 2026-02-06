<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('attribute_set_id')
                    ->label('Özellik Seti')
                    ->relationship('attributeSet', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->placeholder('beden, renk, materyal'),

                TextInput::make('label')
                    ->label('Etiket')
                    ->required()
                    ->placeholder('Beden, Renk, Materyal'),

                Select::make('type')
                    ->label('Tip')
                    ->required()
                    ->options([
                        'select' => 'Seçim (Select)',
                        'text' => 'Metin (Text)',
                        'number' => 'Sayı (Number)',
                        'boolean' => 'Evet/Hayır (Boolean)',
                        'multiselect' => 'Çoklu Seçim (Multiselect)',
                    ])
                    ->default('select'),

                KeyValue::make('options')
                    ->label('Seçenekler')
                    ->keyLabel('Değer')
                    ->valueLabel('Etiket')
                    ->helperText('Select ve Multiselect için kullanılır.')
                    ->visible(fn ($get) => in_array($get('type'), ['select', 'multiselect'])),

                TextInput::make('unit')
                    ->label('Birim')
                    ->placeholder('GB, cm, kg')
                    ->helperText('Sayısal değerler için birim.'),

                TextInput::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_filterable')
                    ->label('Filtrelenebilir')
                    ->helperText('Bu özellik filtrelerde gösterilsin mi?')
                    ->default(false),

                Toggle::make('is_required')
                    ->label('Zorunlu')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
