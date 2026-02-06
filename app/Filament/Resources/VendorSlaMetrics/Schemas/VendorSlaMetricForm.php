<?php

namespace App\Filament\Resources\VendorSlaMetrics\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorSlaMetricForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                DatePicker::make('metric_date')
                    ->required(),
                TextInput::make('total_orders')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('cancelled_orders')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('returned_orders')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('late_shipments')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('on_time_shipments')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('cancel_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('return_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('late_shipment_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('avg_shipment_time')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('avg_response_time')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_questions_answered')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_reviews_responded')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('customer_satisfaction_score')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sla_violations'),
            ]);
    }
}
