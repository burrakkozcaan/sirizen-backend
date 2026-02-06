<?php

namespace App\Filament\Resources\QuickLinks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuickLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('label')
                    ->label('Etiket')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('icon')
                    ->label('İkon')
                    ->alignCenter(),

                TextColumn::make('path')
                    ->label('Yol')
                    ->searchable()
                    ->copyable()
                    ->limit(30),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('campaign.title')
                    ->label('Kampanya')
                    ->searchable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->limit(30),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('order', 'asc')
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
