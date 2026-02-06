<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telefon')
                            ->maxLength(50),

                        TextInput::make('subject')
                            ->label('Konu')
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label('Mesaj')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),

                        Toggle::make('is_read')
                            ->label('Okundu')
                            ->default(false),

                        DateTimePicker::make('replied_at')
                            ->label('Yanıt Tarihi'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
