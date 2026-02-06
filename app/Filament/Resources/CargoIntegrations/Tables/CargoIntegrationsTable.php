<?php

namespace App\Filament\Resources\CargoIntegrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CargoIntegrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shippingCompany.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('integration_type')
                    ->searchable(),
                TextColumn::make('api_endpoint')
                    ->searchable(),
                TextColumn::make('api_key')
                    ->searchable(),
                TextColumn::make('api_secret')
                    ->searchable(),
                TextColumn::make('customer_code')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_test_mode')
                    ->boolean(),
                TextColumn::make('last_sync_at')
                    ->dateTime()
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
