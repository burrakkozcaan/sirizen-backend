<?php

namespace App\Filament\Resources\NotificationSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                IconColumn::make('email_campaigns')
                    ->label('E-posta Kampanya')
                    ->boolean(),

                IconColumn::make('email_orders')
                    ->label('E-posta Sipariş')
                    ->boolean(),

                IconColumn::make('sms_orders')
                    ->label('SMS Sipariş')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('push_enabled')
                    ->label('Push Açık')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('Güncelleme')
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
