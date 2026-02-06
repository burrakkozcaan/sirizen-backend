<?php

namespace App\Filament\Resources\Commissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommissionForm
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

                        TextInput::make('order_item_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('commission_rate')
                            ->numeric()
                            ->required(),

                        TextInput::make('commission_amount')
                            ->numeric()
                            ->required(),

                        TextInput::make('net_amount')
                            ->numeric()
                            ->required(),

                        TextInput::make('status')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
