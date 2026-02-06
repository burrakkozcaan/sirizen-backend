<?php

namespace App\Filament\Resources\FilterConfigs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilterConfigsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('categoryGroup.name')
                    ->label('Kategori Grubu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('filter_type')
                    ->label('Filtre Tipi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attribute' => 'Özellik',
                        'price' => 'Fiyat',
                        'brand' => 'Marka',
                        'rating' => 'Puan',
                        'seller' => 'Satıcı',
                        'campaign' => 'Kampanya',
                        default => $state,
                    }),
                TextColumn::make('attribute.name')
                    ->label('Özellik')
                    ->toggleable(),
                TextColumn::make('display_label')
                    ->label('Görünen Etiket')
                    ->searchable(),
                TextColumn::make('filter_component')
                    ->label('Bileşen')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'checkbox' => 'Onay Kutusu',
                        'radio' => 'Radyo',
                        'range' => 'Aralık',
                        'select' => 'Seçim',
                        'color' => 'Renk',
                        default => $state ?? '-',
                    }),
                TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
                IconColumn::make('is_collapsed')
                    ->label('Kapalı')
                    ->boolean(),
                IconColumn::make('show_count')
                    ->label('Sayı Göster')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
