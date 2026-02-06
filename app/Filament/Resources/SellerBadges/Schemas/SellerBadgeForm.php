<?php

namespace App\Filament\Resources\SellerBadges\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SellerBadgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Rozet Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('URL Kodu')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->readOnly(),

                                TextInput::make('icon')
                                    ->label('İkon (Emoji)')
                                    ->placeholder('⭐')
                                    ->maxLength(255),

                                ColorPicker::make('color')
                                    ->label('Renk'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
