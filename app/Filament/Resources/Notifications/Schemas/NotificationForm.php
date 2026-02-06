<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('user_id')
                            ->numeric(),

                        TextInput::make('order_id')
                            ->numeric(),

                        TextInput::make('shipment_id')
                            ->numeric(),

                        TextInput::make('type'),

                        TextInput::make('channel'),

                        TextInput::make('title'),

                        Textarea::make('message')
                            ->columnSpanFull(),

                        Textarea::make('data')
                            ->columnSpanFull(),

                        DateTimePicker::make('sent_at'),

                        DateTimePicker::make('read_at'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
