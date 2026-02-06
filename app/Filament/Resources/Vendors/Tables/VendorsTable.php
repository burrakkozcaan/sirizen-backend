<?php

namespace App\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        $statusOptions = [
            'pending' => 'İncelemede',
            'active' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'suspended' => 'Askıya alındı',
        ];

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Şirket')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('tier.name')
                    ->label('Seviye')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('categories.name')
                    ->label('Kategoriler')
                    ->formatStateUsing(fn ($state, $record) => $record->categories->pluck('name')->implode(', '))
                    ->searchable(),
                TextColumn::make('company_type')
                    ->label('Şirket türü')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('city')
                    ->label('İl')
                    ->sortable(),
                TextColumn::make('district')
                    ->label('İlçe')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                SelectColumn::make('status')
                    ->label('Durum')
                    ->options($statusOptions)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options($statusOptions),
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
