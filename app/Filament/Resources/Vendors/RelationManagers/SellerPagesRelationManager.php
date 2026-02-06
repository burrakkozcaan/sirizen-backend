<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerPagesRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerPages';

    protected static ?string $title = 'Mağaza Sayfası';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->schema([
                        TextInput::make('seo_slug')
                            ->label('Mağaza URL')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('banner')
                            ->label('Banner')
                            ->image()
                            ->disk('r2')
                            ->directory('sellers/banners')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seo_slug')
                    ->label('Mağaza URL')
                    ->searchable(),

                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('r2')
                    ->square()
                    ->size(60),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
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
