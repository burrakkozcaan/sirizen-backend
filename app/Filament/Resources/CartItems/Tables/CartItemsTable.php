<?php

namespace App\Filament\Resources\CartItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CartItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cart.user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Miktar')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Birim Fiyat')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Toplam')
                    ->getStateUsing(fn ($record) => $record->price * $record->quantity)
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Eklenme')
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
