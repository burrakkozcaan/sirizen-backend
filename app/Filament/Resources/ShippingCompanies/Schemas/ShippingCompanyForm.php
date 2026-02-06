<?php

namespace App\Filament\Resources\ShippingCompanies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('logo'),
                TextInput::make('tracking_url')
                    ->url(),
                TextInput::make('api_url')
                    ->url(),
                Textarea::make('api_credentials')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('webhook_url')
                    ->url(),
                TextInput::make('webhook_secret'),
                TextInput::make('supported_services'),
                TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('price_per_kg')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('price_per_desi')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('free_shipping_threshold')
                    ->numeric(),
                TextInput::make('coverage_areas'),
            ]);
    }
}
