<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use App\OrderItemStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorOrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $title = 'Sipariş Kalemleri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label('Sipariş')
                    ->relationship('order', 'order_number')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'title')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('quantity')
                    ->label('Adet')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('price')
                    ->label('Fiyat')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        OrderItemStatus::PENDING->value => 'Beklemede',
                        OrderItemStatus::PREPARING->value => 'Hazırlanıyor',
                        OrderItemStatus::READY_TO_SHIP->value => 'Kargoya Hazır',
                        OrderItemStatus::SHIPPED->value => 'Kargolandı',
                        OrderItemStatus::DELIVERED->value => 'Teslim Edildi',
                        OrderItemStatus::CANCELLED->value => 'İptal',
                        OrderItemStatus::RETURNED->value => 'İade',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Sipariş')
                    ->sortable(),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Adet')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge(),
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
