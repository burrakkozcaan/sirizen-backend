<?php

namespace App\Filament\Resources\VendorPerformanceLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorPerformanceLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('vendor_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('metric')
                            ->required(),

                        TextInput::make('value')
                            ->numeric()
                            ->required(),

                        DateTimePicker::make('logged_at')
                            ->default(now()),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
