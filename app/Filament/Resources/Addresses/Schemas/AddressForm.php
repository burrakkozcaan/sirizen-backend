<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ev, İş, vb.'),

                        Select::make('address_type')
                            ->label('Adres Tipi')
                            ->options([
                                'home' => 'Ev',
                                'work' => 'İş',
                                'billing' => 'Fatura',
                                'shipping' => 'Teslimat',
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('city')
                                    ->label('Şehir')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('district')
                                    ->label('İlçe')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Textarea::make('address_line')
                            ->label('Adres')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('postal_code')
                                    ->label('Posta Kodu')
                                    ->maxLength(255),

                                Toggle::make('is_default')
                                    ->label('Varsayılan Adres')
                                    ->default(false),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
