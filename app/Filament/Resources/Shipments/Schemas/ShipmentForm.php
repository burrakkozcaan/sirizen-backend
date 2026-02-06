<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kargo Bilgileri')
                    ->description('Takip ve teslimat detaylarını yönetin.')
                    ->columns(2)
                    ->schema([
                        Select::make('order_id')
                            ->label('Sipariş')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $order = \App\Models\Order::with('items')->find($state);
                                    if ($order && $order->items->count() > 0) {
                                        $set('order_item_id', $order->items->first()->id);
                                        $set('vendor_id', $order->items->first()->vendor_id);
                                        $set('address_id', $order->address_id);
                                    }
                                }
                            }),

                        Select::make('order_item_id')
                            ->label('Sipariş Kalemi')
                            ->relationship('orderItem', 'id', fn ($query, $get) => 
                                $query->where('order_id', $get('order_id'))
                            )
                            ->searchable()
                            ->required(),

                        Select::make('shipping_company_id')
                            ->label('Kargo Firması')
                            ->relationship('shippingCompany', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $company = \App\Models\ShippingCompany::find($state);
                                    if ($company) {
                                        $set('carrier', $company->name);
                                    }
                                }
                            }),

                        TextInput::make('carrier')
                            ->label('Kargo Firması (Manuel)')
                            ->maxLength(255),

                        TextInput::make('tracking_number')
                            ->label('Takip Numarası')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('tracking_url')
                            ->label('Takip URL')
                            ->url()
                            ->maxLength(2048),

                        Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'in_transit' => 'Yolda',
                                'out_for_delivery' => 'Dağıtımda',
                                'delivered' => 'Teslim Edildi',
                            ])
                            ->required(),

                        TextInput::make('current_location')
                            ->label('Mevcut Konum')
                            ->maxLength(255),

                        TextInput::make('progress_percent')
                            ->label('İlerleme (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%'),

                        Toggle::make('notify_on_status_change')
                            ->label('Durum değişikliklerinde bildir')
                            ->default(true),

                        DateTimePicker::make('estimated_delivery')
                            ->label('Tahmini Teslim'),

                        DateTimePicker::make('shipped_at')
                            ->label('Kargoya Verildi'),

                        DateTimePicker::make('delivered_at')
                            ->label('Teslim Edildi'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
