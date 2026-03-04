<?php

namespace App\Filament\Resources\Commissions\Schemas;

use App\CommissionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel')
                    ->columns(2)
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('order_item_id')
                            ->label('Sipariş Kalemi')
                            ->relationship('orderItem', 'id')
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Komisyon Detayları')
                    ->columns(2)
                    ->schema([
                        TextInput::make('commission_rate')
                            ->label('Komisyon Oranı (%)')
                            ->numeric()
                            ->suffix('%')
                            ->disabled(),
                        TextInput::make('gross_amount')
                            ->label('Brüt Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        TextInput::make('commission_amount')
                            ->label('Komisyon Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        TextInput::make('net_amount')
                            ->label('Net Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                    ]),

                Section::make('Durum')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Durum')
                            ->options(collect(CommissionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required(),
                        DateTimePicker::make('settled_at')
                            ->label('Kesinleşme Tarihi')
                            ->disabled(),
                        TextInput::make('refunded_amount')
                            ->label('İade Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                    ]),
            ]);
    }
}
