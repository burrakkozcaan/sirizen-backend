<?php

namespace App\Filament\Resources\ReviewImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewImageForm
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
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->product->title.' - '.$record->user->email)
                            ->searchable()
                            ->preload()
                            ->required(),

                        FileUpload::make('image_path')
                            ->label('Görsel')
                            ->image()
                            ->disk('r2')
                            ->directory('reviews/images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor()
                            ->required(),

                        TextInput::make('alt_text')
                            ->label('Alt Metin')
                            ->maxLength(255),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
