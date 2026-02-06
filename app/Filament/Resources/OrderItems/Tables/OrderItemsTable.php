<?php

namespace App\Filament\Resources\OrderItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('order.id')
                    ->label('Order ID')
                    ->sortable(),
                TextColumn::make('product.title')
                    ->label('Product Name')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
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
