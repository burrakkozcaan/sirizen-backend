<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('brand.name')
                    ->label('Marka')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('rating')
                    ->label('Puan')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state >= 4.5 ? 'success' : ($state >= 4 ? 'warning' : 'danger')),

                TextColumn::make('reviews_count')
                    ->label('Yorumlar')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('id', 'desc')
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
