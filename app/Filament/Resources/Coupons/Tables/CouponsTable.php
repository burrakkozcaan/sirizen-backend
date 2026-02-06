<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('discount_value')
                    ->label('İndirim')
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage'
                        ? "%{$state}"
                        : "₺{$state}"
                    )
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
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
