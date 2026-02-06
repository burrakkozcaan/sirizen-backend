<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Sipariş Kalemleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('vendor_id')
                    ->label('Satıcı')
                    ->relationship('vendor', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity')
                    ->label('Adet')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                TextInput::make('price')
                    ->label('Fiyat')
                    ->required()
                    ->numeric()
                    ->prefix('₺'),
                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'preparing' => 'Hazırlanıyor',
                        'ready_to_ship' => 'Kargoya Hazır',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                        'returned' => 'İade Edildi',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Adet')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Toplam')
                    ->money('TRY')
                    ->getStateUsing(fn ($record) => $record->price * $record->quantity),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'preparing' => 'info',
                        'ready_to_ship' => 'info',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'returned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'preparing' => 'Hazırlanıyor',
                        'ready_to_ship' => 'Kargoya Hazır',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                        'returned' => 'İade Edildi',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

