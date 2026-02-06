<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\SimilarProducts\SimilarProductResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class SimilarProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'similarProducts';

    protected static ?string $relatedResource = SimilarProductResource::class;

    protected static ?string $title = 'Benzer Ürünler';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('similar_product_id')
                    ->label('Benzer Ürün')
                    ->relationship(
                        'similarProduct',
                        'title',
                        fn (Builder $query) => $query
                            ->where('category_id', $this->getOwnerRecord()->category_id)
                            ->whereKeyNot($this->getOwnerRecord()->getKey()),
                    )
                    ->searchable()
                    ->preload()
                    ->unique(
                        table: 'similar_products',
                        column: 'similar_product_id',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('product_id', $this->getOwnerRecord()->getKey()),
                    )
                    ->required(),

                Select::make('relation_type')
                    ->label('İlişki Tipi')
                    ->options([
                        'similar' => 'Benzer',
                        'alternative' => 'Alternatif',
                        'cross_sell' => 'Çapraz Satış',
                        'up_sell' => 'Üst Satış',
                    ])
                    ->default('similar')
                    ->required(),

                TextInput::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('similarProduct.title')
                    ->label('Benzer Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('relation_type')
                    ->label('İlişki Tipi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->sortable(),
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
