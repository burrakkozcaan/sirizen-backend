<?php

namespace App\Filament\Resources\VendorDocuments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VendorDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('mime_type'),
                TextInput::make('file_size')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
                TextInput::make('verified_by')
                    ->numeric(),
                DateTimePicker::make('verified_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
