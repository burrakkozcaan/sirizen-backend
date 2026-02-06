<?php

namespace App\Filament\Resources\Translations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TranslationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('locale')
                    ->label('Dil')
                    ->sortable(),
                TextColumn::make('group')
                    ->label('Grup')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Anahtar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Değer')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Güncellenme')
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
