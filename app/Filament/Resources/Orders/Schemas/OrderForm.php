<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('user_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('order_number')
                            ->required(),

                        TextInput::make('total_price')
                            ->numeric()
                            ->required(),

                       Select::make('status')
    ->label('Durum')
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
                        TextInput::make('payment_method')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
