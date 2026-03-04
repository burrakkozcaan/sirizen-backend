<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Toplam Fiyat')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'info',
                        'shipped', 'partially_shipped' => 'primary',
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
                        'refunded' => 'İade Edildi',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Ödeme')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'paid' => 'Ödendi',
                        'pending' => 'Beklemede',
                        'failed' => 'Başarısız',
                        default => $state ?? '-',
                    })
                    ->sortable(),
                TextColumn::make('vendors')
                    ->label('Satıcı')
                    ->getStateUsing(function ($record) {
                        $vendors = $record->items
                            ?->map(fn ($item) => $item->vendor?->name)
                            ->filter()
                            ->unique()
                            ->values();

                        if (! $vendors || $vendors->isEmpty()) {
                            return '-';
                        }

                        return $vendors->implode(', ');
                    })
                    ->wrap(),
                TextColumn::make('items_count')
                    ->label('Ürün Sayısı')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Ödeme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Sipariş Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                        'refunded' => 'İade Edildi',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->options([
                        'paid' => 'Ödendi',
                        'pending' => 'Beklemede',
                        'failed' => 'Başarısız',
                    ]),
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
