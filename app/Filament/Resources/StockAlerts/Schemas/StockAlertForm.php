<?php

namespace App\Filament\Resources\StockAlerts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockAlertForm
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
                            ->required(),

                        Select::make('product_id')
                            ->label('Ürün')
                            ->relationship('product', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        DateTimePicker::make('notified_at')
                            ->label('Bildirim Tarihi'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
