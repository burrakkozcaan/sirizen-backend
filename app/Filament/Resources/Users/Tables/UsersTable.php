<?php

namespace App\Filament\Resources\Users\Tables;

use App\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        UserRole::ADMIN => 'Admin',
                        UserRole::VENDOR => 'Satıcı',
                        UserRole::CUSTOMER => 'Müşteri',
                        default => $state->value ?? 'Bilinmiyor',
                    })
                    ->color(fn ($state) => match ($state) {
                        UserRole::ADMIN => 'danger',
                        UserRole::VENDOR => 'warning',
                        UserRole::CUSTOMER => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_verified')
                    ->label('Onaylı')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
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
