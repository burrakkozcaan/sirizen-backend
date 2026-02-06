<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CampaignsRelationManager extends RelationManager
{
    protected static string $relationship = 'campaigns';

    protected static ?string $title = 'Kampanyalar';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Kampanya')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_type')
                    ->label('Tip')
                    ->badge()
                    ->color('info'),

                TextColumn::make('discount_value')
                    ->label('İndirim')
                    ->formatStateUsing(function ($record): string {
                        if ($record->discount_type === 'percentage') {
                            return '%'.$record->discount_value;
                        }

                        return '₺'.$record->discount_value;
                    }),

                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
