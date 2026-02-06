<?php

namespace App\Filament\Resources\SocialProofRules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialProofRulesTable
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
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cart_count' => 'Sepet Sayısı',
                        'view_count' => 'Görüntülenme',
                        'sold_count' => 'Satış Sayısı',
                        'review_count' => 'Yorum Sayısı',
                        default => $state,
                    }),
                TextColumn::make('display_format')
                    ->label('Görüntü Formatı')
                    ->limit(40),
                TextColumn::make('threshold_type')
                    ->label('Eşik Tipi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'fixed' => 'Sabit',
                        'percentage' => 'Yüzde',
                        default => $state ?? '-',
                    }),
                TextColumn::make('threshold_value')
                    ->label('Eşik Değeri')
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Konum')
                    ->toggleable(),
                TextColumn::make('color')
                    ->label('Renk')
                    ->toggleable(),
                TextColumn::make('icon')
                    ->label('İkon')
                    ->toggleable(),
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
