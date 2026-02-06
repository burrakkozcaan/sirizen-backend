<?php

namespace App\Filament\Resources\PdpLayouts\Schemas;

use App\Models\PdpBlock;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PdpLayoutForm
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

                TextInput::make('name')
                    ->label('Layout Adı')
                    ->required()
                    ->placeholder('Varsayılan, Kampanyalı, Flash Ürün'),

                Toggle::make('is_default')
                    ->label('Varsayılan Layout')
                    ->helperText('Bu kategori grubu için varsayılan olarak kullanılır.'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Builder::make('layout_config.blocks')
                    ->label('PDP Blokları')
                    ->addActionLabel('Blok Ekle')
                    ->blocks([
                        Builder\Block::make('gallery')
                            ->label('Görsel Galeri')
                            ->icon('heroicon-o-photo')
                            ->schema(self::getBlockFields('gallery')),

                        Builder\Block::make('title')
                            ->label('Ürün Başlığı')
                            ->icon('heroicon-o-document-text')
                            ->schema(self::getBlockFields('title')),

                        Builder\Block::make('rating')
                            ->label('Değerlendirme')
                            ->icon('heroicon-o-star')
                            ->schema(self::getBlockFields('rating')),

                        Builder\Block::make('badges')
                            ->label('Rozetler')
                            ->icon('heroicon-o-tag')
                            ->schema(self::getBlockFields('badges')),

                        Builder\Block::make('social_proof')
                            ->label('Sosyal Kanıt')
                            ->icon('heroicon-o-users')
                            ->schema(self::getBlockFields('social_proof')),

                        Builder\Block::make('price')
                            ->label('Fiyat')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema(self::getBlockFields('price')),

                        Builder\Block::make('variant_selector')
                            ->label('Varyant Seçici')
                            ->icon('heroicon-o-swatch')
                            ->schema(self::getBlockFields('variant_selector')),

                        Builder\Block::make('size_selector')
                            ->label('Beden Seçici')
                            ->icon('heroicon-o-scale')
                            ->schema(self::getBlockFields('size_selector')),

                        Builder\Block::make('attributes_highlight')
                            ->label('Öne Çıkan Özellikler')
                            ->icon('heroicon-o-sparkles')
                            ->schema(self::getBlockFields('attributes_highlight')),

                        Builder\Block::make('delivery_info')
                            ->label('Teslimat Bilgisi')
                            ->icon('heroicon-o-truck')
                            ->schema(self::getBlockFields('delivery_info')),

                        Builder\Block::make('campaigns')
                            ->label('Kampanyalar')
                            ->icon('heroicon-o-gift')
                            ->schema(self::getBlockFields('campaigns')),

                        Builder\Block::make('add_to_cart')
                            ->label('Sepete Ekle')
                            ->icon('heroicon-o-shopping-cart')
                            ->schema(self::getBlockFields('add_to_cart')),

                        Builder\Block::make('description')
                            ->label('Ürün Açıklaması')
                            ->icon('heroicon-o-document')
                            ->schema(self::getBlockFields('description')),

                        Builder\Block::make('attributes_detail')
                            ->label('Tüm Özellikler')
                            ->icon('heroicon-o-list-bullet')
                            ->schema(self::getBlockFields('attributes_detail')),

                        Builder\Block::make('reviews')
                            ->label('Değerlendirmeler')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema(self::getBlockFields('reviews')),

                        Builder\Block::make('questions')
                            ->label('Soru & Cevap')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema(self::getBlockFields('questions')),

                        Builder\Block::make('related_products')
                            ->label('Benzer Ürünler')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema(self::getBlockFields('related_products')),
                    ])
                    ->collapsible(),
            ]);
    }

    private static function getBlockFields(string $blockKey): array
    {
        return [
            Select::make('position')
                ->label('Pozisyon')
                ->options([
                    'main' => 'Ana İçerik',
                    'sidebar' => 'Yan Panel',
                    'under_title' => 'Başlık Altı',
                    'bottom' => 'Sayfa Altı',
                ])
                ->default('main')
                ->required(),

            TextInput::make('order')
                ->label('Sıra')
                ->numeric()
                ->default(0)
                ->required(),

            Toggle::make('visible')
                ->label('Görünür')
                ->default(true),
        ];
    }
}
