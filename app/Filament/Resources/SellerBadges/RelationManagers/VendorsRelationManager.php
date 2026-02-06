<?php

namespace App\Filament\Resources\SellerBadges\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendors';

    protected static ?string $title = 'Satıcılar';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pivot.assigned_at')
                    ->label('Atanma')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('pivot.expires_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->headerActions([
                AttachAction::make()
                    ->form([
                        DateTimePicker::make('assigned_at')
                            ->label('Atanma Tarihi')
                            ->default(now()),
                        DateTimePicker::make('expires_at')
                            ->label('Bitiş Tarihi'),
                    ]),
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
