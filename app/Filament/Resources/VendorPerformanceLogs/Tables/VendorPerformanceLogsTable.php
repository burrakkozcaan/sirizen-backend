<?php

namespace App\Filament\Resources\VendorPerformanceLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorPerformanceLogsTable
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
                TextColumn::make('metric')
                    ->label('Metrik')
                    ->searchable(),
                TextColumn::make('value')
                    ->label('Değer')
                    ->sortable(),
                TextColumn::make('period')
                    ->label('Dönem')
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
