<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorPenaltiesRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorPenalties';

    protected static ?string $title = 'Cezalar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('reason')
                    ->label('Sebep')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('penalty_points')
                    ->label('Ceza Puanı')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                DateTimePicker::make('expires_at')
                    ->label('Bitiş Tarihi'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reason')
                    ->label('Sebep')
                    ->limit(50),

                TextColumn::make('penalty_points')
                    ->label('Ceza Puanı')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
