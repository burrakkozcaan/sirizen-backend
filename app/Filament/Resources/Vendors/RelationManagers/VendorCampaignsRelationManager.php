<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VendorCampaignsRelationManager extends RelationManager
{
    protected static string $relationship = 'campaigns';

    protected static ?string $title = 'Kampanyalar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Kampanya Başlığı')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(3)
                    ->maxLength(1000),

                FileUpload::make('banner')
                    ->label('Banner Görseli')
                    ->image()
                    ->disk('r2')
                    ->directory('campaigns/banners')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']),

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
                    ->required()
                    ->numeric()
                    ->minValue(0),

                DateTimePicker::make('starts_at')
                    ->label('Başlangıç Tarihi')
                    ->required()
                    ->default(now()),

                DateTimePicker::make('ends_at')
                    ->label('Bitiş Tarihi')
                    ->required()
                    ->after('starts_at'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('r2')
                    ->square()
                    ->size(60),

                TextColumn::make('title')
                    ->label('Kampanya')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_value')
                    ->label('İndirim')
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage'
                        ? "%{$state}"
                        : "₺{$state}"
                    )
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

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
