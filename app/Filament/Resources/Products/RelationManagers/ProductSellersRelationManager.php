<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ProductSellersRelationManager extends RelationManager
{
    protected static string $relationship = 'productSellers';

    protected static ?string $title = 'Satıcılar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->label('Satıcı')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload()
                    ->unique(
                        table: 'product_sellers',
                        column: 'vendor_id',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule
                            ->where('product_id', $this->getOwnerRecord()->getKey())
                            ->where('variant_id', request()->input('variant_id') ?? request()->input('data.variant_id')),
                    )
                    ->required(),

                Select::make('variant_id')
                    ->label('Varyant')
                    ->relationship('variant', 'sku', fn ($query) => $query->where('product_id', $this->getOwnerRecord()->getKey()))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->sku
                            ?: trim(($record->color ?? '') . ' ' . ($record->size ?? ''))
                            ?: (string) $record->id
                    ),

                TextInput::make('price')
                    ->label('Fiyat')
                    ->numeric()
                    ->prefix('₺')
                    ->minValue(0)
                    ->required(),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('dispatch_days')
                    ->label('Kargoya Verme Süresi (gün)')
                    ->numeric()
                    ->minValue(0)
                    ->default(3)
                    ->required(),

                Select::make('shipping_type')
                    ->label('Kargo Türü')
                    ->required()
                    ->options([
                        'normal' => 'Normal',
                        'express' => 'Express',
                        'same_day' => 'Aynı Gün',
                    ])
                    ->default('normal'),

                Toggle::make('is_featured')
                    ->label('Öne Çıkan')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variant.sku')
                    ->label('Varyant')
                    ->default('-'),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('dispatch_days')
                    ->label('Kargo Süresi')
                    ->numeric()
                    ->suffix(' gün'),

                TextColumn::make('shipping_type')
                    ->label('Kargo Türü')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'express' => 'Express',
                        'same_day' => 'Aynı Gün',
                        default => 'Normal',
                    }),

                IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
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
