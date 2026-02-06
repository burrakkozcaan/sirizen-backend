<?php

namespace App\Filament\Resources\VendorPayouts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; 
use Filament\Tables\Columns\TextColumn\TextColumnSize;

class VendorPayoutsTable
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
                TextColumn::make('amount')
                    ->label('Amount')
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
