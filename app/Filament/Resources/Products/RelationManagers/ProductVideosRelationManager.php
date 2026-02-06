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

class ProductVideosRelationManager extends RelationManager
{
    protected static string $relationship = 'videos';

    protected static ?string $title = 'Ürün Videoları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Başlık')
                    ->maxLength(255),

                TextInput::make('url')
                    ->label('Video URL')
                    ->url()
                    ->required(),

                Select::make('video_type')
                    ->label('Video Türü')
                    ->options([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'other' => 'Diğer',
                    ])
                    ->default('youtube')
                    ->required(),

                TextInput::make('order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_featured')
                    ->label('Öne Çıkan')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->limit(30),

                TextColumn::make('url')
                    ->label('URL')
                    ->url(fn ($record) => $record->url)
                    ->limit(30),

                TextColumn::make('video_type')
                    ->label('Tür')
                    ->enum([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'other' => 'Diğer',
                    ]),

                TextColumn::make('order')
                    ->label('Sıra'),

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
