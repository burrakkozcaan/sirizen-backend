<?php

namespace App\Filament\Resources\VendorBalances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; 
use Filament\Tables\Columns\TextColumn\TextColumnSize;
class VendorBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor Name')
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Balance')
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
