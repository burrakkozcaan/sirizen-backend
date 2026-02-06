<?php

namespace App\Filament\Resources\RecentlyVieweds\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecentlyViewedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('user_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('product_id')
                            ->numeric()
                            ->required(),

                        DateTimePicker::make('viewed_at')
                            ->default(now()),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
