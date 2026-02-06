<?php

namespace App\Filament\Resources\ShippingRules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable()
                    ->default('Genel Kural'),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                TextColumn::make('address.title')
                    ->label('Adres')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                TextColumn::make('cutoff_time')
                    ->label('Son Gönderim Saati')
                    ->time()
                    ->sortable(),
                IconColumn::make('same_day_shipping')
                    ->label('Aynı Gün Kargo')
                    ->boolean(),
                IconColumn::make('free_shipping')
                    ->label('Ücretsiz Kargo')
                    ->boolean(),
                TextColumn::make('free_shipping_min_amount')
                    ->label('Min. Tutar')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime()
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
