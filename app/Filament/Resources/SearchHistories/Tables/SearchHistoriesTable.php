<?php

namespace App\Filament\Resources\SearchHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SearchHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('query')
                    ->label('Arama')
                    ->searchable(),

                TextColumn::make('results_count')
                    ->label('Sonuç')
                    ->sortable(),

                TextColumn::make('searched_at')
                    ->label('Arama Tarihi')
                    ->dateTime('d/m/Y H:i')
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
