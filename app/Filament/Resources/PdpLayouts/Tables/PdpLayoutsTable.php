<?php

namespace App\Filament\Resources\PdpLayouts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PdpLayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categoryGroup.name')
                    ->label('Kategori Grubu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Layout Adı')
                    ->searchable(),

                IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i'),
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
