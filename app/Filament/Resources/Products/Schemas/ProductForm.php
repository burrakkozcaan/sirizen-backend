<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Ürün Detayları')
                    ->tabs([
                        Tab::make('Genel')
                            ->schema([
                                Section::make('Ürün Bilgileri')
                                    ->description('Temel katalog bilgilerini yönetin.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('brand_id')
                                            ->label('Marka')
                                            ->relationship('brand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Select::make('category_id')
                                            ->label('Kategori')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        TextInput::make('title')
                                            ->label('Ürün Başlığı')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL için kullanılacak'),

                                        TextInput::make('price')
                                            ->label('Fiyat')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->minValue(0),

                                        TextInput::make('discount_price')
                                            ->label('İndirimli Fiyat')
                                            ->numeric()
                                            ->prefix('₺')
                                            ->minValue(0),
                                    ]),
                            ]),
                        Tab::make('İçerik')
                            ->schema([
                                Section::make('Kısa Açıklama')
                                    ->description('Ürünün öne çıkan kısa açıklamasını yazın.')
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->label('Kısa Açıklama')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Açıklama')
                                    ->description('Ürün detaylarını açıklayın.')
                                    ->schema([
                                        MarkdownEditor::make('description')
                                            ->label('Açıklama')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Ek Bilgiler')
                                    ->description('Ürüne özel ek bilgileri ve notları paylaşın. Her satır bir madde olarak kaydedilir.')
                                    ->schema([
                                        MarkdownEditor::make('additional_information')
                                            ->label('Ek Bilgiler (Markdown)')
                                            ->columnSpanFull(),
                                        Textarea::make('additional_info')
                                            ->label('Ek Bilgiler (Her satır bir madde)')
                                            ->helperText('Her satır bir madde olarak array\'e dönüştürülür.')
                                            ->rows(5)
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->additional_info) {
                                                    $array = is_string($record->additional_info) 
                                                        ? json_decode($record->additional_info, true) 
                                                        : $record->additional_info;
                                                    if (is_array($array)) {
                                                        $component->state(implode("\n", $array));
                                                    }
                                                }
                                            })
                                            ->dehydrated(true)
                                            ->dehydrateStateUsing(function ($state) {
                                                if (is_string($state) && !empty(trim($state))) {
                                                    $lines = array_filter(
                                                        array_map('trim', explode("\n", $state)),
                                                        fn($line) => !empty($line)
                                                    );
                                                    return json_encode(array_values($lines));
                                                }
                                                return null;
                                            }),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Ürün Güvenliği')
                            ->schema([
                                Section::make('Ürün Güvenliği Bilgileri')
                                    ->description('Ürün güvenliği uyarı ve açıklamalarını girin.')
                                    ->schema([
                                        MarkdownEditor::make('safety_information')
                                            ->label('Ürün Güvenliği Bilgileri')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Üretici Bilgileri')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('manufacturer_name')
                                            ->label('Üretici')
                                            ->maxLength(255),
                                        TextInput::make('manufacturer_contact')
                                            ->label('Üretici İletişim')
                                            ->maxLength(255),
                                        Textarea::make('manufacturer_address')
                                            ->label('Üretici Adresi')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Ürün Sorumlusu')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('responsible_party_name')
                                            ->label('Ürün Sorumlusu')
                                            ->maxLength(255),
                                        TextInput::make('responsible_party_contact')
                                            ->label('Sorumlu İletişim')
                                            ->maxLength(255),
                                        Textarea::make('responsible_party_address')
                                            ->label('Sorumlu Adresi')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Durum')
                            ->schema([
                                Section::make('Puan ve Yayın')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('rating')
                                            ->label('Puan')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(5)
                                            ->step(0.1),

                                        TextInput::make('reviews_count')
                                            ->label('Yorum Sayısı')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
