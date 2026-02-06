<?php

namespace App\Filament\Resources\PlatformRevenueReports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlatformRevenueReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('period_type')
                    ->searchable(),
                TextColumn::make('total_revenue')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_commission')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vendor_payouts')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_orders')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_vendors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('active_vendors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('new_vendors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_customers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('new_customers')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_products')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('avg_order_value')
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
