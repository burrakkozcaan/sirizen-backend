<?php

namespace App\Filament\Resources\ReturnPolicies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReturnPolicyForm
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
                        TextInput::make('days')
                            ->required()
                            ->numeric()
                            ->default(15),
                        Toggle::make('is_free')
                            ->required(),
                        Textarea::make('conditions')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
