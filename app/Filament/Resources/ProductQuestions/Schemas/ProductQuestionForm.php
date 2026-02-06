<?php

namespace App\Filament\Resources\ProductQuestions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'title')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->columnSpanFull(),
                Toggle::make('answered_by_vendor')
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name'),
                TextInput::make('product_question_category_id')
                    ->numeric(),
            ]);
    }
}
