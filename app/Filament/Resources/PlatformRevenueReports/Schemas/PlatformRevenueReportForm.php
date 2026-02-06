<?php

namespace App\Filament\Resources\PlatformRevenueReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlatformRevenueReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('report_date')
                    ->required(),
                TextInput::make('period_type')
                    ->required(),
                TextInput::make('total_revenue')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_commission')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('vendor_payouts')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_orders')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_vendors')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('active_vendors')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('new_vendors')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_customers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('new_customers')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_products')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('avg_order_value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('top_categories'),
                TextInput::make('top_vendors'),
            ]);
    }
}
