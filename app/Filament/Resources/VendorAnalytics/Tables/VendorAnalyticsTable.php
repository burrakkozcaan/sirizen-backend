<?php

namespace App\Filament\Resources\VendorAnalytics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorAnalyticsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tarih')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('total_sales')
                    ->label('Toplam Satış')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('total_orders')
                    ->label('Sipariş')
                    ->sortable(),
                TextColumn::make('units_sold')
                    ->label('Satılan Adet')
                    ->sortable(),
                TextColumn::make('average_order_value')
                    ->label('Ortalama Sipariş')
                    ->money('TRY')
                    ->toggleable(),
                TextColumn::make('commission_amount')
                    ->label('Komisyon')
                    ->money('TRY')
                    ->toggleable(),
                TextColumn::make('net_earnings')
                    ->label('Net Kazanç')
                    ->money('TRY')
                    ->toggleable(),
                TextColumn::make('active_products')
                    ->label('Aktif Ürün')
                    ->toggleable(),
                TextColumn::make('conversion_rate')
                    ->label('Dönüşüm %')
                    ->suffix('%')
                    ->toggleable(),
                TextColumn::make('average_rating')
                    ->label('Ortalama Puan')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
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
