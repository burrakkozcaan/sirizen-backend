<?php

namespace App\Filament\Resources\PriceAlerts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceAlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('target_price')
                    ->label('Hedef Fiyat')
                    ->money('TRY')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('notified_at')
                    ->label('Bildirim')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
