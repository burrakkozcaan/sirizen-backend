<?php

namespace App\Filament\Resources\VendorDailyStats\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorDailyStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                DatePicker::make('stat_date')
                    ->required(),
                TextInput::make('total_sales')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('revenue')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('commission')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('net_revenue')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('orders_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('products_sold')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('new_customers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('returning_customers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('avg_order_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('page_views')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('product_views')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('conversion_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
