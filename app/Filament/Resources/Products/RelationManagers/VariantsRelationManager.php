<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Ürün Varyantları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('size')
                    ->label('Beden')
                    ->maxLength(50),

                TextInput::make('color')
                    ->label('Renk')
                    ->maxLength(50),

                TextInput::make('price')
                    ->label('Fiyat')
                    ->numeric()
                    ->prefix('₺')
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                TextInput::make('weight')
                    ->label('Ağırlık (gr)')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('size')
                    ->label('Beden')
                    ->badge()
                    ->color('info'),

                TextColumn::make('color')
                    ->label('Renk')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                TextColumn::make('weight')
                    ->label('Ağırlık')
                    ->numeric()
                    ->suffix(' gr'),
            ])
            ->defaultSort('sku')
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
