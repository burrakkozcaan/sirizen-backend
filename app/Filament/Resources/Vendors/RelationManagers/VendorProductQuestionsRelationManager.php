<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorProductQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'productQuestions';

    protected static ?string $title = 'Ürün Soru & Cevapları';

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

                Textarea::make('question')
                    ->label('Soru')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('answer')
                    ->label('Cevap')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('answered_by_vendor')
                    ->label('Satıcı Yanıtladı')
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

                TextColumn::make('question')
                    ->label('Soru')
                    ->limit(40),

                TextColumn::make('answer')
                    ->label('Cevap')
                    ->limit(40),

                IconColumn::make('answered_by_vendor')
                    ->label('Yanıtlandı')
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
