<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorProductReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'productReviews';

    protected static ?string $title = 'Ürün Yorumları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship(
                        'product',
                        'title',
                        fn ($query) => $query->whereHas('productSellers', fn ($vendorQuery) => $vendorQuery
                            ->where('vendor_id', $this->getOwnerRecord()->getKey())),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('rating')
                    ->label('Puan')
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
                    ->columnSpanFull(),

                Textarea::make('vendor_response')
                    ->label('Satıcı Yanıtı')
                    ->rows(3)
                    ->columnSpanFull(),

                DateTimePicker::make('vendor_response_at')
                    ->label('Yanıt Tarihi'),

                Toggle::make('is_verified_purchase')
                    ->label('Doğrulanmış Satın Alma')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Puan')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(40),

                TextColumn::make('vendor_response')
                    ->label('Satıcı Yanıtı')
                    ->limit(40),

                IconColumn::make('is_verified_purchase')
                    ->label('Doğrulandı')
                    ->boolean(),
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
