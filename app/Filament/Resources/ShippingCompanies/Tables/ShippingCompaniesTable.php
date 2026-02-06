<?php

namespace App\Filament\Resources\ShippingCompanies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingCompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('logo')
                    ->searchable(),
                TextColumn::make('tracking_url')
                    ->searchable(),
                TextColumn::make('api_url')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('order')
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
                TextColumn::make('webhook_url')
                    ->searchable(),
                TextColumn::make('webhook_secret')
                    ->searchable(),
                TextColumn::make('base_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('price_per_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_per_desi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('free_shipping_threshold')
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
