<?php

namespace App\Filament\Resources\SimilarProducts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SimilarProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'title')
                            ->required(),
                        Select::make('similar_product_id')
                            ->relationship('similarProduct', 'title')
                            ->required(),
                        TextInput::make('score')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('relation_type')
                            ->required()
                            ->default('similar'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
