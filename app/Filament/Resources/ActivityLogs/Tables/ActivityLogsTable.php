<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('activity')
                    ->label('Aktivite')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
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
