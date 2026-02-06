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

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $title = 'Ürün Özellikleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Özellik Adı')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Kumaş, Renk, Materyal vb.'),

                TextInput::make('value')
                    ->label('Değer')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('%100 Pamuk, Siyah, Deri vb.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Özellik')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('value')
                    ->label('Değer')
                    ->searchable()
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('key')
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
