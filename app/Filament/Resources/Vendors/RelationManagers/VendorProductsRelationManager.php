<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class VendorProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'productSellers';

    protected static ?string $title = 'Satıştaki Ürünler';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'title')
                    ->searchable()
                    ->preload()
                    ->unique(
                        table: 'product_sellers',
                        column: 'product_id',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule
                            ->where('vendor_id', $this->getOwnerRecord()->getKey())
                            ->where('variant_id', request()->input('variant_id') ?? request()->input('data.variant_id')),
                    )
                    ->required()
                    ->createOptionForm([
                        TextInput::make('title')
                            ->label('Ürün Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug'),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('brand_id')
                            ->label('Marka')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $vendor = $this->getOwnerRecord();

                        $product = Product::create([
                            'title' => $data['title'],
                            'slug' => $data['slug'],
                            'category_id' => $data['category_id'],
                            'brand_id' => $data['brand_id'],
                            'description' => $data['description'] ?? null,
                            'vendor_id' => $vendor->id,
                            'is_active' => true,
                            'price' => 0,
                            'stock' => 0,
                        ]);

                        return $product->id;
                    })
                    ->createOptionModalHeading('Yeni Ürün Oluştur'),

                Select::make('variant_id')
                    ->label('Varyant')
                    ->relationship('variant', 'sku')
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
                TextColumn::make('product.title')
                    ->label('Ürün')
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
