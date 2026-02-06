<?php

namespace App\Filament\Resources\CouponUsages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponUsagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('coupon.code')
                    ->label('Kupon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                TextColumn::make('order.order_number')
                    ->label('Sipariş')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('discount_amount')
                    ->label('İndirim')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('used_at')
                    ->label('Kullanım Tarihi')
                    ->dateTime('d/m/Y H:i')
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
