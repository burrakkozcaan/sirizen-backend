<?php

namespace App\Filament\Resources\PriceHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('Varyant SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant.product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('İndirimli Fiyat')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
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
