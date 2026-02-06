<?php

namespace App\Filament\Resources\SearchLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SearchLogsTable
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
                    ->toggleable(),
                TextColumn::make('query')
                    ->label('Arama Sorgusu')
                    ->searchable(),
                TextColumn::make('results_count')
                    ->label('Sonuç Sayısı')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->toggleable(),
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
