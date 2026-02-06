<?php

namespace App\Filament\Resources\Campaigns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('r2')
                    ->square()
                    ->size(80),

                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Kampanya Başlığı')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description),

                \Filament\Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                \Filament\Tables\Columns\BadgeColumn::make('discount_type')
                    ->label('İndirim Tipi')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Yüzde',
                        'fixed' => 'Sabit',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'percentage',
                        'primary' => 'fixed',
                    ]),

                \Filament\Tables\Columns\TextColumn::make('discount_value')
                    ->label('İndirim Miktarı')
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage'
                        ? "%{$state}"
                        : "₺{$state}"
                    )
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('discount_type')
                    ->label('İndirim Tipi')
                    ->options([
                        'percentage' => 'Yüzde',
                        'fixed' => 'Sabit',
                    ]),

                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
