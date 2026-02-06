<?php

namespace App\Filament\Resources\BadgeRules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BadgeRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('badgeDefinition.label')
                    ->label('Badge')
                    ->searchable(),

                TextColumn::make('categoryGroup.name')
                    ->label('Kategori Grubu')
                    ->placeholder('Tümü')
                    ->searchable(),

                TextColumn::make('condition_type')
                    ->label('Koşul')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'price_discount' => 'İndirim %',
                        'review_count' => 'Değerlendirme',
                        'rating' => 'Puan',
                        'stock' => 'Stok',
                        'is_new' => 'Yeni',
                        'is_bestseller' => 'Çok Satan',
                        'price' => 'Fiyat',
                        'discount_price' => 'İndirimli Fiyat',
                        'custom' => 'Özel',
                        default => $state,
                    }),

                TextColumn::make('condition_config')
                    ->label('Koşul Detayı')
                    ->formatStateUsing(function ($record) {
                        $config = $record->condition_config;
                        $op = $config['operator'] ?? '=';
                        $val = $config['value'] ?? '-';

                        return "{$op} {$val}";
                    }),

                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
