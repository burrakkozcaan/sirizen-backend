<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Marka Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('URL Kodu')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->readOnly(),

                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->disk('r2')
                            ->directory('brands/logos')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
