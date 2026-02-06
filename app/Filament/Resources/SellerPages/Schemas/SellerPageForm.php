<?php

namespace App\Filament\Resources\SellerPages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SellerPageForm
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

                        TextInput::make('seo_slug')
                            ->label('Mağaza URL')
                            ->required(),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->disk('r2')
                            ->directory('sellers/logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->helperText('Mağaza logosu (Maksimum 2MB)'),

                        FileUpload::make('banner')
                            ->label('Banner')
                            ->image()
                            ->disk('r2')
                            ->directory('sellers/banners')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->helperText('Mağaza banner görseli (Maksimum 5MB)'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
