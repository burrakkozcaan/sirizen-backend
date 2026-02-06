<?php

namespace App\Filament\Resources\VendorTiers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('min_total_orders')
                            ->numeric()
                            ->default(0),

                        TextInput::make('min_rating')
                            ->numeric()
                            ->default(0),

                        TextInput::make('max_cancel_rate')
                            ->numeric()
                            ->default(100),

                        TextInput::make('max_return_rate')
                            ->numeric()
                            ->default(100),

                        TextInput::make('commission_rate')
                            ->numeric()
                            ->default(10),

                        TextInput::make('max_products')
                            ->numeric(),

                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),

                        TextInput::make('priority_boost')
                            ->numeric()
                            ->default(0),

                        TextInput::make('badge_icon'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
