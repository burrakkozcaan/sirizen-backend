<?php

namespace App\Filament\Resources\DataDeletionRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DataDeletionRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('request_type')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('processed_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
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
