<?php

namespace App\Filament\Resources\VendorBalances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorBalanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('vendor_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('balance')
                            ->numeric()
                            ->default(0),

                        TextInput::make('pending_balance')
                            ->numeric()
                            ->default(0),

                        DateTimePicker::make('last_settlement_at'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
