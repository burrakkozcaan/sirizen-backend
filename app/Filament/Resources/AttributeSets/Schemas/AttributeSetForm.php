<?php

namespace App\Filament\Resources\AttributeSets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttributeSetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('dis_giyim_erkek, telefon_aksesuar'),

                TextInput::make('name')
                    ->label('Ad')
                    ->required()
                    ->placeholder('Dış Giyim - Erkek'),

                Select::make('category_group_id')
                    ->label('Kategori Grubu')
                    ->relationship('categoryGroup', 'name')
                    ->searchable()
                    ->preload(),

                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(3),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
