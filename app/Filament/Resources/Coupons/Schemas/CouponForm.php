<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('product_id')
                            ->label('Ürün')
                            ->relationship('product', 'title')
                            ->searchable()
                            ->preload(),

                        TextInput::make('code')
                            ->label('Kupon Kodu')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('discount_type')
                            ->label('İndirim Tipi')
                            ->options([
                                'percentage' => 'Yüzde (%)',
                                'fixed' => 'Sabit Tutar (TL)',
                            ])
                            ->required()
                            ->default('percentage'),

                        TextInput::make('discount_value')
                            ->label('İndirim Miktarı')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('min_order_amount')
                            ->label('Minimum Sepet Tutarı')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('max_discount_amount')
                            ->label('Maksimum İndirim')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('usage_limit')
                            ->label('Toplam Kullanım Limiti')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('per_user_limit')
                            ->label('Kullanıcı Başına Limit')
                            ->numeric()
                            ->minValue(0),

                        DateTimePicker::make('starts_at')
                            ->label('Başlangıç')
                            ->default(now()),

                        DateTimePicker::make('expires_at')
                            ->label('Bitiş'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
