<?php

namespace App\Filament\Resources\SocialProofRules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SocialProofRuleForm
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

                Select::make('type')
                    ->label('Sosyal Kanıt Tipi')
                    ->required()
                    ->options([
                        'cart_count' => 'Sepet Sayısı',
                        'view_count' => 'Görüntüleme Sayısı',
                        'sold_count' => 'Satış Sayısı',
                        'review_count' => 'Değerlendirme Sayısı',
                    ]),

                TextInput::make('display_format')
                    ->label('Görünüm Formatı')
                    ->required()
                    ->placeholder('{count} kişinin sepetinde')
                    ->helperText('{count} değişkeni sayıyı temsil eder.'),

                Select::make('threshold_type')
                    ->label('Eşik Tipi')
                    ->options([
                        'fixed' => 'Sabit Değer',
                        'percentage' => 'Yüzde',
                    ])
                    ->default('fixed'),

                TextInput::make('threshold_value')
                    ->label('Eşik Değeri')
                    ->numeric()
                    ->default(0)
                    ->helperText('Bu değerden az ise gösterilmez.'),

                TextInput::make('refresh_interval')
                    ->label('Yenileme Aralığı (saniye)')
                    ->numeric()
                    ->default(300)
                    ->helperText('Verinin kaç saniyede bir güncelleneceği.'),

                Select::make('position')
                    ->label('Pozisyon')
                    ->options([
                        'under_title' => 'Başlık Altı',
                        'near_price' => 'Fiyat Yanı',
                        'under_gallery' => 'Galeri Altı',
                    ])
                    ->default('under_title'),

                TextInput::make('color')
                    ->label('Renk')
                    ->placeholder('#10b981 veya green'),

                TextInput::make('icon')
                    ->label('İkon')
                    ->placeholder('heroicon-o-users'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
