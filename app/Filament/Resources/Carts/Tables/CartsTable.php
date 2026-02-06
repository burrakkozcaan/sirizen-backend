<?php

namespace App\Filament\Resources\Carts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label('Ürün Sayısı')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Toplam')
                    ->getStateUsing(function ($record) {
                        return $record->items->sum(function ($item) {
                            return $item->price * $item->quantity;
                        });
                    })
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Kullanıcı'),
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
