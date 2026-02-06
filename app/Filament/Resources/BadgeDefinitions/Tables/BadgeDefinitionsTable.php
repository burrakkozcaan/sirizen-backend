<?php

namespace App\Filament\Resources\BadgeDefinitions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BadgeDefinitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Etiket')
                    ->searchable(),

                ColorColumn::make('color')
                    ->label('Renk'),

                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('rules_count')
                    ->label('Kural Sayısı')
                    ->counts('rules'),
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
