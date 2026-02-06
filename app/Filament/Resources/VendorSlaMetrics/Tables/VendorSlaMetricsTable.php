<?php

namespace App\Filament\Resources\VendorSlaMetrics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorSlaMetricsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('metric_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cancelled_orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('returned_orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_shipments')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('on_time_shipments')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cancel_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('return_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_shipment_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_shipment_time')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_response_time')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_questions_answered')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_reviews_responded')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer_satisfaction_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
