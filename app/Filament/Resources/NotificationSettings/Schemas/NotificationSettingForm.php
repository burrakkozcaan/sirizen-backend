<?php

namespace App\Filament\Resources\NotificationSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationSettingForm
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
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Section::make('E-posta Bildirimleri')
                            ->columns(2)
                            ->schema([
                                Toggle::make('email_campaigns')
                                    ->label('Kampanyalar')
                                    ->default(true),
                                Toggle::make('email_orders')
                                    ->label('Siparişler')
                                    ->default(true),
                                Toggle::make('email_promotions')
                                    ->label('Promosyonlar')
                                    ->default(true),
                                Toggle::make('email_reviews')
                                    ->label('Yorumlar')
                                    ->default(true),
                            ])
                            ->columnSpanFull(),

                        Section::make('SMS Bildirimleri')
                            ->columns(2)
                            ->schema([
                                Toggle::make('sms_campaigns')
                                    ->label('Kampanyalar')
                                    ->default(false),
                                Toggle::make('sms_orders')
                                    ->label('Siparişler')
                                    ->default(true),
                                Toggle::make('sms_promotions')
                                    ->label('Promosyonlar')
                                    ->default(false),
                            ])
                            ->columnSpanFull(),

                        Section::make('Push Bildirimleri')
                            ->columns(2)
                            ->schema([
                                Toggle::make('push_enabled')
                                    ->label('Push Açık')
                                    ->default(true),
                                Toggle::make('push_campaigns')
                                    ->label('Kampanyalar')
                                    ->default(true),
                                Toggle::make('push_orders')
                                    ->label('Siparişler')
                                    ->default(true),
                                Toggle::make('push_messages')
                                    ->label('Mesajlar')
                                    ->default(true),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
