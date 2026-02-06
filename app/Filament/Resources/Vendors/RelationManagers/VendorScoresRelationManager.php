<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

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

class VendorScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorScores';

    protected static ?string $title = 'Puanlar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('total_score')
                    ->label('Toplam Puan')
                    ->numeric()
                    ->default(0),

                TextInput::make('delivery_score')
                    ->label('Teslimat Puanı')
                    ->numeric()
                    ->default(0),

                TextInput::make('rating_score')
                    ->label('Puanlama Skoru')
                    ->numeric()
                    ->default(0),

                TextInput::make('stock_score')
                    ->label('Stok Skoru')
                    ->numeric()
                    ->default(0),

                TextInput::make('support_score')
                    ->label('Destek Skoru')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('total_score')
                    ->label('Toplam')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('delivery_score')
                    ->label('Teslimat')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('rating_score')
                    ->label('Puanlama')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock_score')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('support_score')
                    ->label('Destek')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->vendorScores()->doesntExist()),
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
