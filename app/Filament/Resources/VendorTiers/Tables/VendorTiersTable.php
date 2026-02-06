<?php

namespace App\Filament\Resources\VendorTiers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VendorTiersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Adı')
                    ->sortable(),
                TextColumn::make('min_total_orders')
                    ->label('Min Total Orders')
                    ->sortable(),
                TextColumn::make('min_rating')
                    ->label('Min Rating')
                    ->sortable(),
                TextColumn::make('max_cancel_rate')
                    ->label('Max Cancel Rate')
                    ->sortable(),
                TextColumn::make('max_return_rate')
                    ->label('Max Return Rate')
                    ->sortable(),
                TextColumn::make('commission_rate')
                    ->label('Commission Rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_products')
                    ->label('Max Products')
                    ->numeric()
                    ->sortable(),
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
