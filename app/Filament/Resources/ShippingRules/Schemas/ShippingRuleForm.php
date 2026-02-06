<?php

namespace App\Filament\Resources\ShippingRules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShippingRuleForm
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
                            ->preload()
                            ->nullable()
                            ->helperText('Boş bırakılırsa genel kural olur'),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Belirli kullanıcı için özel kural'),

                        Select::make('address_id')
                            ->label('Adres')
                            ->relationship('address', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Belirli adres için özel kural'),

                        TimePicker::make('cutoff_time')
                            ->label('Son Gönderim Saati')
                            ->helperText('Bu saatten sonraki siparişler ertesi gün kargoya verilir'),

                        Toggle::make('same_day_shipping')
                            ->label('Aynı Gün Kargo')
                            ->helperText('Sipariş aynı gün kargoya verilir'),

                        Toggle::make('free_shipping')
                            ->label('Ücretsiz Kargo')
                            ->helperText('Kargo ücreti satıcı tarafından karşılanır'),

                        TextInput::make('free_shipping_min_amount')
                            ->label('Ücretsiz Kargo Minimum Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->helperText('Bu tutarın üzerindeki siparişlerde ücretsiz kargo'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
