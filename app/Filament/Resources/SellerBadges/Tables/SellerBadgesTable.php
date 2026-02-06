<?php

namespace App\Filament\Resources\SellerBadges\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerBadgesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('İkon')
                    ->alignCenter()
                    ->size(TextSize::Large),

                TextColumn::make('name')
                    ->label('Rozet Adı')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('slug')
                    ->label('URL Kodu')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('color')
                    ->label('Renk')
                    ->badge()
                    ->color(fn ($state) => $state ?: 'gray'),

                TextColumn::make('vendors_count')
                    ->label('Satıcı Sayısı')
                    ->counts('vendors')
                    ->badge()
                    ->color('success'),

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
            ->defaultSort('name')
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
