<?php

namespace App\Filament\Resources\QuickLinks\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuickLinkForm
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
                                TextInput::make('key')
                                    ->label('Anahtar')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Select::make('label')
                                    ->options([
                                        'price_drops' => 'Fiyatı Düşenler',
                                        'super_deals' => 'Süper Fırsatlar',
                                        'food' => 'Yiyecek',
                                        'electronics' => 'Elektronik',
                                        'fashion' => 'Moda',
                                        'home_living' => 'Ev & Yaşam',
                                        'cosmetics' => 'Kozmetik',
                                        'shoes_bags' => 'Ayakkabı & Çanta',
                                        'mother_baby' => 'Anne & Bebek',
                                        'sports' => 'Spor',
                                        'points' => 'Puan',
                                        'custom' => 'Özel',
                                    ])
                                   
                                    ->label('Etiket')
                                    ->required(),
                                

                                Select::make('icon')
                                    ->label('İkon')
                                    ->options([
                                        'trending_down' => 'Trending Down',
                                        'zap' => 'Super Deals',
                                        'utensils' => 'Food',
                                        'tv' => 'Electronics',
                                        'shirt' => 'Fashion',
                                        'armchair' => 'Home & Living',
                                        'sparkles' => 'Cosmetics',
                                        'shopping_bag' => 'Shoes & Bags',
                                        'baby' => 'Mother & Baby',
                                        'dumbbell' => 'Sports',
                                        'gift' => 'Points',
                                    ])
                                    ->required(),

                                Select::make('link_type')
                                    ->label('Link Türü')
                                    ->options([
                                        'category' => 'Kategori',
                                        'campaign' => 'Kampanya',
                                        'product' => 'Ürün',
                                        'custom' => 'Özel',
                                    ])
                                    ->default('category')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Clear path when link_type changes
                                        $set('path', null);
                                        // Clear relational fields
                                        if ($state !== 'category') {
                                            $set('category_slug', null);
                                        }
                                        if ($state !== 'campaign') {
                                            $set('campaign_slug', null);
                                        }
                                        if ($state !== 'product') {
                                            $set('product_id', null);
                                        }
                                    }),

                                Select::make('color')
                                    ->label('Renk Teması')
                                    ->options([
                                        'primary' => 'Primary',
                                        'danger' => 'Danger (Kırmızı)',
                                        'warning' => 'Warning (Sarı)',
                                        'success' => 'Success (Yeşil)',
                                        'info' => 'Info (Mavi)',
                                        'purple' => 'Purple (Mor)',
                                    ])
                                    ->default('primary'),

                                TextInput::make('order')
                                    ->label('Sıra')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),

                        // Link Type Based Fields
                        Section::make('Link Ayarları')
                            ->schema([
                                // Category Selection
                                Select::make('category_slug')
                                    ->label('Kategori')
                                    ->relationship('category', 'name', fn ($query) => $query)
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->getSearchResultsUsing(function ($search) {
                                        return \App\Models\Category::where('name', 'like', "%{$search}%")->pluck('name', 'slug');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('link_type') === 'category')
                                    ->reactive()
                                    ->required(fn ($get) => $get('link_type') === 'category')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('path', "/category/{$state}");
                                        }
                                    }),

                                // Campaign Selection
                                Select::make('campaign_slug')
                                    ->label('Kampanya')
                                    ->relationship('campaign', 'title', fn ($query) => $query)
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title)
                                    ->getSearchResultsUsing(function ($search) {
                                        return \App\Models\Campaign::where('title', 'like', "%{$search}%")->pluck('title', 'slug');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('link_type') === 'campaign')
                                    ->reactive()
                                    ->required(fn ($get) => $get('link_type') === 'campaign')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('path', "/campaign/{$state}");
                                        }
                                    }),

                                // Product Selection
                                Select::make('product_id')
                                    ->label('Ürün')
                                    ->relationship('product', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('link_type') === 'product')
                                    ->reactive()
                                    ->required(fn ($get) => $get('link_type') === 'product')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('path', "/product/{$product->slug}");
                                            }
                                        }
                                    }),

                                // Custom Path Input
                                TextInput::make('path')
                                    ->label('Özel Yol')
                                    ->placeholder('/kampanya/yilbasi')
                                    ->maxLength(255)
                                    ->visible(fn ($get) => $get('link_type') === 'custom')
                                    ->required(fn ($get) => $get('link_type') === 'custom')
                                    ->reactive(),

                                // Auto-generated Path (Read-only)
                                TextInput::make('path')
                                    ->label('Yol')
                                    ->disabled()
                                    ->dehydrated() // Save to DB
                                    ->visible(fn ($get) => in_array($get('link_type'), ['category', 'campaign', 'product']))
                                    ->placeholder(function ($get) {
                                        $type = $get('link_type');
                                        if ($type === 'category') {
                                            return '/category/{slug} otomatik oluşacak';
                                        }
                                        if ($type === 'campaign') {
                                            return '/campaign/{slug} otomatik oluşacak';
                                        }
                                        if ($type === 'product') {
                                            return '/product/{slug} otomatik oluşacak';
                                        }
                                        return '';
                                    }),

                                // Preview
                                Placeholder::make('preview')
                                    ->label('Önizleme')
                                    ->content(function ($get) {
                                        $path = $get('path');
                                        if (!$path) {
                                            return '<span class="text-muted-foreground">Yol henüz oluşturulmadı</span>';
                                        }
                                        return "<span class='font-mono text-sm'>{$path}</span>";
                                    })
                                    ->visible(fn ($get) => filled($get('path'))),
                            ])
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
