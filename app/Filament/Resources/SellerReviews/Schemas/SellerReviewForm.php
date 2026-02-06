<?php

namespace App\Filament\Resources\SellerReviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SellerReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Satıcı')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('delivery_rating')
                            ->label('Teslimat Puanı')
                            ->options([
                                1 => '1 - Çok Kötü',
                                2 => '2 - Kötü',
                                3 => '3 - Orta',
                                4 => '4 - İyi',
                                5 => '5 - Mükemmel',
                            ])
                            ->required(),

                        Select::make('communication_rating')
                            ->label('İletişim Puanı')
                            ->options([
                                1 => '1 - Çok Kötü',
                                2 => '2 - Kötü',
                                3 => '3 - Orta',
                                4 => '4 - İyi',
                                5 => '5 - Mükemmel',
                            ])
                            ->required(),

                        Select::make('packaging_rating')
                            ->label('Paketleme Puanı')
                            ->options([
                                1 => '1 - Çok Kötü',
                                2 => '2 - Kötü',
                                3 => '3 - Orta',
                                4 => '4 - İyi',
                                5 => '5 - Mükemmel',
                            ])
                            ->required(),

                        Textarea::make('comment')
                            ->label('Yorum')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
