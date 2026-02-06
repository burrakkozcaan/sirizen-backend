<?php

namespace App\Filament\Resources\RecentlyVieweds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RecentlyViewedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->sortable(),
                TextColumn::make('viewed_at')
                    ->label('Görüntülenme Tarihi')
                    ->dateTime()
                    ->sortable(),
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
