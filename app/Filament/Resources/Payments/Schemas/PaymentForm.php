<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\PaymentProvider;
use App\PaymentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Ödeme detayları')
                    ->schema([
                        TextInput::make('order_id')
                            ->label('Sipariş ID')
                            ->numeric()
                            ->required()
                            ->disabled(),

                        TextInput::make('user_id')
                            ->label('Kullanıcı ID')
                            ->numeric()
                            ->required()
                            ->disabled(),

                        TextInput::make('amount')
                            ->label('Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),

                        TextInput::make('currency')
                            ->label('Para Birimi')
                            ->default('TRY')
                            ->disabled(),

                        Select::make('payment_provider')
                            ->label('Ödeme Gateway')
                            ->options(collect(PaymentProvider::cases())->mapWithKeys(fn ($provider) => [$provider->value => $provider->label()]))
                            ->required(),

                        Select::make('status')
                            ->label('Durum')
                            ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                            ->required(),

                        TextInput::make('transaction_id')
                            ->label('İşlem ID')
                            ->disabled(),

                        TextInput::make('checkout_token')
                            ->label('Checkout Token')
                            ->disabled(),

                        TextInput::make('installment_count')
                            ->label('Taksit Sayısı')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12),

                        DateTimePicker::make('paid_at')
                            ->label('Ödeme Tarihi'),
                    ])
                    ->columns(2),

                Section::make('Komisyon Dağılımı')
                    ->description('Ödeme dağılım detayları (otomatik hesaplanır)')
                    ->schema([
                        TextInput::make('commission_amount')
                            ->label('Platform Komisyonu')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),

                        TextInput::make('vendor_amount')
                            ->label('Satıcı Payı')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),

                        TextInput::make('platform_amount')
                            ->label('Platform Payı')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),

                        Select::make('split_status')
                            ->label('Dağılım Durumu')
                            ->options([
                                'pending' => 'Bekliyor',
                                'settled' => 'Dağıtıldı',
                            ])
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('İade Bilgileri')
                    ->description('İade işlem detayları')
                    ->schema([
                        TextInput::make('refund_id')
                            ->label('İade ID')
                            ->disabled(),

                        TextInput::make('refunded_amount')
                            ->label('İade Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),

                        DateTimePicker::make('refunded_at')
                            ->label('İade Tarihi')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Gateway Detayları')
                    ->description('Ödeme gateway yanıt bilgileri')
                    ->schema([
                        TextInput::make('callback_status')
                            ->label('Callback Durumu')
                            ->disabled(),

                        DateTimePicker::make('callback_received_at')
                            ->label('Callback Alınma Tarihi')
                            ->disabled(),

                        TextInput::make('error_message')
                            ->label('Hata Mesajı')
                            ->disabled()
                            ->columnSpanFull(),

                        KeyValue::make('metadata')
                            ->label('Metadata')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
