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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorCouponsRelationManager extends RelationManager
{
    protected static string $relationship = 'coupons';

    protected static ?string $title = 'Kuponlar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'title')
                    ->searchable()
                    ->preload(),

                TextInput::make('code')
                    ->label('Kupon Kodu')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('discount_type')
                    ->label('İndirim Tipi')
                    ->options([
                        'percentage' => 'Yüzde (%)',
                        'fixed' => 'Sabit Tutar (TL)',
                    ])
                    ->required()
                    ->default('percentage'),

                TextInput::make('discount_value')
                    ->label('İndirim Miktarı')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                TextInput::make('min_order_amount')
                    ->label('Minimum Sepet Tutarı')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('max_discount_amount')
                    ->label('Maksimum İndirim')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('usage_limit')
                    ->label('Toplam Kullanım Limiti')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('per_user_limit')
                    ->label('Kullanıcı Başına Limit')
                    ->numeric()
                    ->minValue(0),

                DateTimePicker::make('starts_at')
                    ->label('Başlangıç')
                    ->default(now()),

                DateTimePicker::make('expires_at')
                    ->label('Bitiş'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),

                TextColumn::make('discount_value')
                    ->label('İndirim')
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage'
                        ? "%{$state}"
                        : "₺{$state}"
                    )
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_active')
                    ->label('Aktif')
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
