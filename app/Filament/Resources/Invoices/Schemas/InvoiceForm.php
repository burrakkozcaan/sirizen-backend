<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('invoice_number')
                    ->required(),
                TextInput::make('invoice_type')
                    ->required(),
                TextInput::make('invoice_scenario')
                    ->required()
                    ->default('basic'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('TRY'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('uuid')
                    ->label('UUID'),
                Textarea::make('ettn')
                    ->columnSpanFull(),
                TextInput::make('invoice_data'),
                TextInput::make('receiver_info'),
                Textarea::make('error_message')
                    ->columnSpanFull(),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('cancelled_at'),
            ]);
    }
}
