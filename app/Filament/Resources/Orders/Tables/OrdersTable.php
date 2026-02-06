<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Toplam Fiyat')
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'info',
                        'shipped', 'partially_shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'partially_shipped' => 'Kısmen Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Ürün Sayısı')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
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
