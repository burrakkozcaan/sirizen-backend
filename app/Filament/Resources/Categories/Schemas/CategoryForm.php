<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Bilgileri')
                    ->description('Hiyerarşi ve görünürlük ayarlarını yönetin.')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Üst Kategori')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Ana kategori olarak bırakın'),

                        TextInput::make('name')
                            ->label('Kategori Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL için kullanılacak'),

                        TextInput::make('icon')
                            ->label('Icon (Emoji)')
                            ->maxLength(10)
                            ->placeholder('👗')
                            ->helperText('Kategori için emoji icon'),

                        TextInput::make('order')
                            ->label('Sıralama')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Kategori listesinde sıralama'),
                    ]),
                Section::make('Görsel')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Görsel')
                            ->image()
                            ->disk('r2')
                            ->directory('categories/images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor(),
                    ]),
            ]);
    }
}
