<?php

namespace App\Filament\Resources\Refunds\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RefundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        TextInput::make('order_item_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('user_id')
                            ->numeric()
                            ->required(),

                        TextInput::make('vendor_id')
                            ->numeric()
                            ->required(),

                        Textarea::make('reason')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('status')
                            ->required(),

                        TextInput::make('refund_amount')
                            ->numeric()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
