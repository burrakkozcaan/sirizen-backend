<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        \Filament\Forms\Components\Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload(),

                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Kampanya Başlığı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                        \Filament\Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->maxLength(1000),

                        \Filament\Forms\Components\FileUpload::make('banner')
                            ->label('Banner Görseli')
                            ->image()
                            ->disk('r2')
                            ->directory('campaigns/banners')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->helperText('Banner görseli R2\'ye yüklenecek'),

                        \Filament\Forms\Components\Select::make('discount_type')
                            ->label('İndirim Tipi')
                            ->options([
                                'percentage' => 'Yüzde (%)',
                                'fixed' => 'Sabit Tutar (TL)',
                            ])
                            ->required()
                            ->default('percentage'),

                        \Filament\Forms\Components\TextInput::make('discount_value')
                            ->label('İndirim Miktarı')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        \Filament\Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Başlangıç Tarihi')
                            ->required()
                            ->default(now()),

                        \Filament\Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Bitiş Tarihi')
                            ->required()
                            ->after('starts_at'),

                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
