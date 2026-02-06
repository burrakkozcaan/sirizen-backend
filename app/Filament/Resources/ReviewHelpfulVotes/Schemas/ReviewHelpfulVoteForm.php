<?php

namespace App\Filament\Resources\ReviewHelpfulVotes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewHelpfulVoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('product_review_id')
                            ->label('Ürün Yorumu')
                            ->relationship('productReview', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Toggle::make('is_helpful')
                            ->label('Faydalı')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
