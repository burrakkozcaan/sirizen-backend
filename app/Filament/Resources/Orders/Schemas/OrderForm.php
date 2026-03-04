<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Müşteri')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->required(),
                        TextInput::make('order_number')
                            ->label('Sipariş No')
                            ->required(),
                        TextInput::make('total_price')
                            ->label('Toplam Fiyat')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        TextInput::make('payment_method')
                            ->label('Ödeme Yöntemi'),
                    ])
                    ->columnSpanFull(),

                Section::make('Sipariş Durumu')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Sipariş Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'confirmed' => 'Onaylandı',
                                'processing' => 'Hazırlanıyor',
                                'shipped' => 'Kargoda',
                                'delivered' => 'Teslim Edildi',
                                'cancelled' => 'İptal Edildi',
                                'refunded' => 'İade Edildi',
                            ])
                            ->required(),
                        Select::make('payment_status')
                            ->label('Ödeme Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'failed' => 'Başarısız',
                            ])
                            ->disabled(),
                        DateTimePicker::make('paid_at')
                            ->label('Ödeme Tarihi')
                            ->disabled(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
