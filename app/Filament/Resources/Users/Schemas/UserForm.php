<?php

namespace App\Filament\Resources\Users\Schemas;

use App\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make('Kullanıcı Bilgileri')
                        ->description('Temel profil ve erişim bilgilerini güncelleyin.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Ad Soyad')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('E-posta')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            TextInput::make('phone')
                                ->label('Telefon')
                                ->tel()
                                ->maxLength(255),

                            TextInput::make('password')
                                ->label('Şifre')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn ($context) => $context === 'create')
                                ->maxLength(255),
                        ]),
                    Section::make('Rol ve Durum')
                        ->schema([
                            Select::make('role')
                                ->label('Rol')
                                ->options([
                                    UserRole::CUSTOMER->value => 'Müşteri',
                                    UserRole::VENDOR->value => 'Satıcı',
                                    UserRole::ADMIN->value => 'Admin',
                                ])
                                ->required()
                                ->default(UserRole::CUSTOMER->value),

                            Toggle::make('is_verified')
                                ->label('Onaylı')
                                ->default(false),
                        ])
                        ->grow(false),
                ])->from('md'),
            ]);
    }
}
