<?php

namespace App\Filament\Resources\Commissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionsTable
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
                    ->sortable(),
                TextColumn::make('orderItem.order.id')
                    ->label('Order ID')
                    ->sortable(),
                TextColumn::make('orderItem.order.user.name')
                    ->label('Kullanıcı')
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Komisyon')
                    ->money('TRY')
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
