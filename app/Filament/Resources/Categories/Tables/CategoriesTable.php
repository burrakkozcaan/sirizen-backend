<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                ImageColumn::make('image')
                    ->label('Görsel')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name)),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->default('-'),

                TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->default('Ana Kategori'),

                TextColumn::make('children_count')
                    ->label('Alt Kategoriler')
                    ->counts('children')
                    ->badge()
                    ->color('success'),

                TextColumn::make('order')
                    ->label('Sıra')
                    ->numeric()
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
            ->defaultSort('order')
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
