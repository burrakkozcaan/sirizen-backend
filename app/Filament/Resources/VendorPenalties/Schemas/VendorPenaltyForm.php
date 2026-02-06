<?php

namespace App\Filament\Resources\VendorPenalties\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorPenaltyForm
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

                        Textarea::make('reason')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('penalty_points')
                            ->numeric()
                            ->required(),

                        DateTimePicker::make('expires_at'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
