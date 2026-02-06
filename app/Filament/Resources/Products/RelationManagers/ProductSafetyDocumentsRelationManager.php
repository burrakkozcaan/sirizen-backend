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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductSafetyDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'safetyDocuments';

    protected static ?string $title = 'Ürün Güvenliği Belgeleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('file')
                    ->label('Belge')
                    ->disk('r2')
                    ->directory('products/safety-documents')
                    ->visibility('public')
                    ->maxSize(10240)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'image/*',
                    ])
                    ->required(),

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
                TextColumn::make('title')
                    ->label('Başlık')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('file')
                    ->label('Dosya')
                    ->limit(40),

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
