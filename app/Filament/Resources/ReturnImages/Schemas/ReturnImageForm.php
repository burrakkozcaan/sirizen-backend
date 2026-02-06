<?php

namespace App\Filament\Resources\ReturnImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReturnImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('product_return_id')
                            ->label('Ürün İadesi')
                            ->relationship('productReturn', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),

                        FileUpload::make('image')
                            ->label('Görsel')
                            ->image()
                            ->disk('r2')
                            ->directory('returns/images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
