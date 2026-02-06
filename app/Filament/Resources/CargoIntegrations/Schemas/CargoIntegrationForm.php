<?php

namespace App\Filament\Resources\CargoIntegrations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CargoIntegrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shipping_company_id')
                    ->relationship('shippingCompany', 'name')
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name'),
                TextInput::make('integration_type')
                    ->required(),
                TextInput::make('api_endpoint'),
                TextInput::make('api_key'),
                TextInput::make('api_secret'),
                TextInput::make('customer_code'),
                TextInput::make('api_credentials'),
                TextInput::make('configuration'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_test_mode')
                    ->required(),
                DateTimePicker::make('last_sync_at'),
                Textarea::make('last_error')
                    ->columnSpanFull(),
            ]);
    }
}
