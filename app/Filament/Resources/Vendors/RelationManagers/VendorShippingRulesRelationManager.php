<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorShippingRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'shippingRules';

    protected static ?string $title = 'Kargo Kuralları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TimePicker::make('cutoff_time')
                    ->label('Kesim Saati'),

                Toggle::make('same_day_shipping')
                    ->label('Aynı Gün Kargo')
                    ->required(),

                Toggle::make('free_shipping')
                    ->label('Ücretsiz Kargo')
                    ->required(),

                TextInput::make('free_shipping_min_amount')
                    ->label('Ücretsiz Kargo Limiti')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cutoff_time')
                    ->label('Kesim Saati')
                    ->sortable(),

                IconColumn::make('same_day_shipping')
                    ->label('Aynı Gün')
                    ->boolean(),

                IconColumn::make('free_shipping')
                    ->label('Ücretsiz')
                    ->boolean(),

                TextColumn::make('free_shipping_min_amount')
                    ->label('Limit')
                    ->money('TRY')
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
