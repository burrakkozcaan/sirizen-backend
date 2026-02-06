<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Tables;

use App\PaymentProvider;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentGatewaySettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentProvider ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof PaymentProvider ? $state->color() : 'gray')
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Görünen Ad')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('is_test_mode')
                    ->label('Test Modu')
                    ->boolean()
                    ->trueIcon('heroicon-o-beaker')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('warning')
                    ->falseColor('success'),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
