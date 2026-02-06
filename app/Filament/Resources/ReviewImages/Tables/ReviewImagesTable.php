<?php

namespace App\Filament\Resources\ReviewImages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReviewImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('productReview.product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('productReview.user.email')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('r2')
                    ->getStateUsing(function ($record) {
                        if (! $record->image_path) {
                            return null;
                        }

                        return Storage::disk('r2')->exists($record->image_path)
                            ? $record->image_path
                            : null;
                    })
                    ->defaultImageUrl('/images/placeholder-brand.png')
                    ->square()
                    ->size(60),

                TextColumn::make('sort_order')
                    ->label('Sıralama')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
