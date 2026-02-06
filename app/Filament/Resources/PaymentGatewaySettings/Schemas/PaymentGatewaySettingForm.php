<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Schemas;

use App\PaymentProvider;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentGatewaySettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->schema([
                        Select::make('provider')
                            ->label('Provider')
                            ->options(collect(PaymentProvider::cases())->mapWithKeys(fn ($provider) => [$provider->value => $provider->label()]))
                            ->required()
                            ->disabled(),

                        TextInput::make('display_name')
                            ->label('Görünen Ad')
                            ->required()
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Bu gateway ödeme seçeneklerinde görünsün mü?'),

                        Toggle::make('is_test_mode')
                            ->label('Test Modu')
                            ->helperText('Test modunda gerçek ödeme alınmaz'),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0)
                            ->helperText('Düşük değer daha önce gösterilir'),
                    ])
                    ->columns(2),

                Section::make('API Kimlik Bilgileri')
                    ->description('Güvenli bir şekilde şifrelenerek saklanır')
                    ->schema([
                        KeyValue::make('credentials')
                            ->label('Kimlik Bilgileri')
                            ->keyLabel('Anahtar')
                            ->valueLabel('Değer')
                            ->addActionLabel('Yeni Ekle')
                            ->helperText('PayTR: merchant_id, merchant_key, merchant_salt | iyzico: api_key, secret_key')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Konfigürasyon')
                    ->description('Gateway özel ayarları')
                    ->schema([
                        KeyValue::make('configuration')
                            ->label('Ayarlar')
                            ->keyLabel('Ayar')
                            ->valueLabel('Değer')
                            ->addActionLabel('Yeni Ekle')
                            ->helperText('Örn: max_installment, locale, currency')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
