<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerReviews';

    protected static ?string $title = 'Satıcı Değerlendirmeleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'email')
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
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                TextColumn::make('delivery_rating')
                    ->label('Teslimat')
                    ->sortable(),

                TextColumn::make('communication_rating')
                    ->label('İletişim')
                    ->sortable(),

                TextColumn::make('packaging_rating')
                    ->label('Paketleme')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(40),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
