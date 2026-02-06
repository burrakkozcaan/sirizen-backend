<?php

namespace App\Filament\Resources\ProductImportLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductImportLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('file_type')
                    ->required(),
                TextInput::make('total_rows')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('success_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('failed_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('skipped_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('errors'),
                Textarea::make('summary')
                    ->columnSpanFull(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
