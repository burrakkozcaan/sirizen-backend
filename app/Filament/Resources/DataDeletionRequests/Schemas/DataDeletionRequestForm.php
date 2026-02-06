<?php

namespace App\Filament\Resources\DataDeletionRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DataDeletionRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('request_type')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('reason')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                TextInput::make('processed_by')
                    ->numeric(),
                DateTimePicker::make('requested_at')
                    ->required(),
                DateTimePicker::make('processed_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
