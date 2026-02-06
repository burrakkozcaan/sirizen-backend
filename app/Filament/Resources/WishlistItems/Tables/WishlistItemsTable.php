<?php

namespace App\Filament\Resources\WishlistItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WishlistItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wishlist.name')
                    ->label('İstek Listesi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('wishlist.user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
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
