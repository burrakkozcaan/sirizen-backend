<?php

namespace App\Filament\Resources\BadgeRules\Schemas;

use App\Models\BadgeDefinition;
use App\Models\CategoryGroup;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BadgeRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('badge_definition_id')
                    ->label('Badge')
                    ->relationship('badgeDefinition', 'label')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('category_group_id')
                    ->label('Kategori Grubu (Opsiyonel)')
                    ->relationship('categoryGroup', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Boş bırakılırsa tüm kategoriler için geçerli olur.'),

                Select::make('condition_type')
                    ->label('Koşul Tipi')
                    ->required()
                    ->options([
                        'price_discount' => 'İndirim Yüzdesi',
                        'review_count' => 'Değerlendirme Sayısı',
                        'rating' => 'Puan',
                        'stock' => 'Stok Miktarı',
                        'is_new' => 'Yeni Ürün',
                        'is_bestseller' => 'Çok Satan',
                        'price' => 'Fiyat',
                        'discount_price' => 'İndirimli Fiyat',
                        'custom' => 'Özel Alan',
                    ])
                    ->live(),

                Select::make('condition_config.operator')
                    ->label('Operatör')
                    ->required()
                    ->options([
                        '=' => 'Eşit (=)',
                        '!=' => 'Eşit Değil (!=)',
                        '>' => 'Büyük (>)',
                        '>=' => 'Büyük Eşit (>=)',
                        '<' => 'Küçük (<)',
                        '<=' => 'Küçük Eşit (<=)',
                        'in' => 'İçinde (in)',
                        'contains' => 'İçerir (contains)',
                    ]),

                TextInput::make('condition_config.value')
                    ->label('Değer')
                    ->required()
                    ->helperText('Sayısal değerler için direkt yazın. Liste için virgülle ayırın.'),

                TextInput::make('condition_config.field')
                    ->label('Özel Alan')
                    ->visible(fn ($get) => $get('condition_type') === 'custom')
                    ->helperText('Product modelindeki alan adı'),

                TextInput::make('priority')
                    ->label('Öncelik')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
