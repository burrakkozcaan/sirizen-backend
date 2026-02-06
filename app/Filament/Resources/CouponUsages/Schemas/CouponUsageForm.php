<?php

namespace App\Filament\Resources\CouponUsages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponUsageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('coupon_id')
                            ->label('Kupon')
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('order_id')
                            ->label('Sipariş')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload(),

                        TextInput::make('discount_amount')
                            ->label('İndirim Tutarı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        DateTimePicker::make('used_at')
                            ->label('Kullanım Tarihi'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
