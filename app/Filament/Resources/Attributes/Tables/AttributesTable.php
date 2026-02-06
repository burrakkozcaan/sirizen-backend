<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attributeSet.name')
                    ->label('Özellik Seti')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Key')
                    ->searchable(),

                TextColumn::make('label')
                    ->label('Etiket')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'select' => 'Seçim',
                        'text' => 'Metin',
                        'number' => 'Sayı',
                        'boolean' => 'Evet/Hayır',
                        'multiselect' => 'Çoklu Seçim',
                        default => $state,
                    }),

                IconColumn::make('is_filterable')
                    ->label('Filtrelenebilir')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
