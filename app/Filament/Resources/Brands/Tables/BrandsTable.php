<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl('/images/placeholder-brand.png'),

                TextColumn::make('name')
                    ->label('Marka Adı')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('slug')
                    ->label('URL Kodu')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('name', 'asc')
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
