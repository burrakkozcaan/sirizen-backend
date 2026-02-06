<?php

namespace App\Filament\Resources\SellerReviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('delivery_rating')
                    ->label('Teslimat')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->alignCenter(),

                TextColumn::make('communication_rating')
                    ->label('İletişim')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->alignCenter(),

                TextColumn::make('packaging_rating')
                    ->label('Paketleme')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->alignCenter(),

                TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
