<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Ürün Görselleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('url')
                    ->label('Görsel')
                    ->image()
                    ->disk('r2')
                    ->directory('products/images')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->required(),

                TextInput::make('alt')
                    ->label('Alt Text')
                    ->maxLength(255),

                TextInput::make('order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_main')
                    ->label('Ana Görsel')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('url')
                    ->label('Görsel')
                    ->size(60),

                TextColumn::make('alt')
                    ->label('Alt Text')
                    ->limit(30),

                TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_main')
                    ->label('Ana Görsel')
                    ->boolean()
                    ->alignCenter(),
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
