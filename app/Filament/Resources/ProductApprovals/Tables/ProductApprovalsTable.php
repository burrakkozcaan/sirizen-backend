<?php

namespace App\Filament\Resources\ProductApprovals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class ProductApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),
                TextColumn::make('rejection_reason')
                    ->label('Red Nedeni')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('admin_notes')
                    ->label('Admin Notları')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('reviewer.name')
                    ->label('İnceleyen')
                    ->toggleable(),
                TextColumn::make('submitted_at')
                    ->label('Gönderilme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label('İncelenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
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
