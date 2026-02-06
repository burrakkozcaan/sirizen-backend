<?php

namespace App\Filament\Resources\VendorFollowers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorFollowerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
