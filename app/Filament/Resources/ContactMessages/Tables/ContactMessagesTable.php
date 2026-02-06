<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(40)
                    ->searchable(),

                IconColumn::make('is_read')
                    ->label('Okundu')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Gönderim')
                    ->dateTime('d/m/Y H:i')
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
