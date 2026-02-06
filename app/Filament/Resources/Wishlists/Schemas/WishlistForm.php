<?php

namespace App\Filament\Resources\Wishlists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WishlistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Liste Adı')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
