<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductBannersRelationManager extends RelationManager
{
    protected static string $relationship = 'productBanners';

    protected static ?string $title = 'Ürün Bannerları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image')
                    ->label('Banner Görseli')
                    ->image()
                    ->disk('r2')
                    ->directory('products/banners')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->required(),

                Select::make('position')
                    ->label('Konum')
                    ->options([
                        'top_left' => 'Sol Üst',
                        'top_right' => 'Sağ Üst',
                        'under_gallery' => 'Galeri Altı',
                    ])
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Banner')
                    ->size(60),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('position')
                    ->label('Konum')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
