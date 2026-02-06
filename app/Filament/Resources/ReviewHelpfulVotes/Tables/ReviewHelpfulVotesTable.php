<?php

namespace App\Filament\Resources\ReviewHelpfulVotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewHelpfulVotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('productReview.product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Kullanıcı')
                    ->searchable(),

                IconColumn::make('is_helpful')
                    ->label('Faydalı')
                    ->boolean(),

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
