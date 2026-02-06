<?php

namespace App\Filament\Resources\VendorPayouts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorPayoutForm
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

                        TextInput::make('amount')
                            ->numeric()
                            ->required(),

                        TextInput::make('payout_method')
                            ->required(),

                        TextInput::make('status')
                            ->required(),

                        DatePicker::make('period_start')
                            ->required(),

                        DatePicker::make('period_end')
                            ->required(),

                        DateTimePicker::make('paid_at'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
