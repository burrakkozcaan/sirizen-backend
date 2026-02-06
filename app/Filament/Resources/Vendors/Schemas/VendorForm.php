<?php

namespace App\Filament\Resources\Vendors\Schemas;

use App\CompanyType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        $statusOptions = [
            'pending' => 'İncelemede',
            'active' => 'Onaylandı',
            'suspended' => 'Askıya alındı',
        ];

        return $schema
            ->components([
                Tabs::make('Mağaza Detayları')
                    ->tabs([
                        Tab::make('Genel')
                            ->schema([
                                Section::make('Mağaza Bilgileri')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Kullanıcı')
                                            ->relationship('user', 'email')
                                            ->searchable()
                                            ->required(),
                                        Select::make('tier_id')
                                            ->label('Seviye')
                                            ->relationship('tier', 'name')
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('name')
                                            ->label('Şirket ismi')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->label('Mağaza URL')
                                            ->required(),
                                        Select::make('categories')
                                            ->label('Kategoriler')
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->columnSpanFull(),
                                        Select::make('company_type')
                                            ->label('Şirket türü')
                                            ->options(CompanyType::class)
                                            ->enum(CompanyType::class)
                                            ->required(),
                                    ]),
                            ]),
                        Tab::make('Vergi ve Konum')
                            ->schema([
                                Section::make('Yasal Bilgiler')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('tax_number')
                                            ->label('Vergi kimlik numarası')
                                            ->required(),
                                        TextInput::make('city')
                                            ->label('İl')
                                            ->required(),
                                        TextInput::make('district')
                                            ->label('İlçe')
                                            ->required(),
                                        TextInput::make('reference_code')
                                            ->label('Referans kodu'),
                                    ]),
                            ]),
                        Tab::make('Açıklama')
                            ->schema([
                                Section::make('Mağaza Açıklaması')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Açıklama')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Durum')
                            ->schema([
                                Section::make('Onay Durumu')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Durum')
                                            ->options($statusOptions)
                                            ->required(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
