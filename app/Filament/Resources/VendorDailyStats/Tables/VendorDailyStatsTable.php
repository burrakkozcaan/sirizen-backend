<?php

namespace App\Filament\Resources\VendorDailyStats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorDailyStatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('stat_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_sales')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('revenue')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('commission')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_revenue')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('orders_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('products_sold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('new_customers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('returning_customers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_order_value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('page_views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('product_views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('conversion_rate')
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
