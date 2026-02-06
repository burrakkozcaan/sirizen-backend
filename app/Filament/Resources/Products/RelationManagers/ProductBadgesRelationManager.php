<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductBadgesRelationManager extends RelationManager
{
    protected static string $relationship = 'productBadges';

    protected static ?string $title = 'Ürün Badge\'leri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Etiket')
                    ->required()
                    ->maxLength(255),

                Select::make('color')
                    ->label('Renk')
                    ->options([
                        'danger' => 'Kırmızı',
                        'warning' => 'Sarı',
                        'success' => 'Yeşil',
                        'info' => 'Mavi',
                        'gray' => 'Gri',
                    ])
                    ->default('info')
                    ->required(),

                TextInput::make('icon')
                    ->label('İkon')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Etiket')
                    ->badge()
                    ->color(fn ($state, $record) => $record->color ?: 'gray')
                    ->searchable(),

                TextColumn::make('icon')
                    ->label('İkon')
                    ->placeholder('-'),
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
