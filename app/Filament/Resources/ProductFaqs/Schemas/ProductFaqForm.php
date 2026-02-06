<?php

namespace App\Filament\Resources\ProductFaqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductFaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'title'),
                        Select::make('category_id')
                            ->relationship('category', 'name'),
                        Textarea::make('question')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
