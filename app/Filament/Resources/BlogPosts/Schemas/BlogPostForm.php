<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel Bilgiler')
                    ->description('Kaydı düzenlemek için gerekli alanları doldurun.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Yazar')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Textarea::make('excerpt')
                            ->label('Özet')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_published')
                            ->label('Yayınlandı')
                            ->default(false),

                        DateTimePicker::make('published_at')
                            ->label('Yayın Tarihi'),
                    ])
                    ->columnSpanFull(),

                Section::make('İçerik')
                    ->schema([
                        MarkdownEditor::make('content')
                            ->label('İçerik')
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columnSpanFull(),

                Section::make('Görsel')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('r2')
                            ->directory('blog/cover-images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageEditor(),
                    ])
                    ->columnSpanFull(),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Başlık')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
