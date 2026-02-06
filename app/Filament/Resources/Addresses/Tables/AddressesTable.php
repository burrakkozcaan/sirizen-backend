<?php

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddressesTable
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
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('address_type')
                    ->label('Tip')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('district')
                    ->label('İlçe')
                    ->searchable(),

                TextColumn::make('address_line')
                    ->label('Adres')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('postal_code')
                    ->label('Posta Kodu')
                    ->searchable(),

                IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
