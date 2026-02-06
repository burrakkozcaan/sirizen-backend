<?php

namespace App\Filament\Resources\ProductImportLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductImportLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->searchable(),
                TextColumn::make('file_name')
                    ->searchable(),
                TextColumn::make('file_type')
                    ->searchable(),
                TextColumn::make('total_rows')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('success_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('failed_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('skipped_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('started_at')
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
