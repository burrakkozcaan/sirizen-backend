<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductSafetyImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'safetyImages';

    protected static ?string $title = 'Ürün Güvenliği Görselleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label('Görsel')
                    ->image()
                    ->disk('r2')
                    ->directory('products/safety-images')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->required(),

                TextInput::make('title')
                    ->label('Başlık')
                    ->maxLength(255),

                TextInput::make('alt')
                    ->label('Alt Metin')
                    ->maxLength(255),

                TextInput::make('order')
                    ->label('Sıra')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->size(60),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('order')
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
