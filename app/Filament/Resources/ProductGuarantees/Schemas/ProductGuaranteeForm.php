<?php

namespace App\Filament\Resources\ProductGuarantees\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductGuaranteeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('product_id')
                            ->required()
                            ->numeric(),
                        TextInput::make('type')
                            ->required(),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
