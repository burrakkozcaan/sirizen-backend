<?php

namespace App\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        $statusOptions = [
            'pending' => 'İncelemede',
            'active' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'suspended' => 'Askıya alındı',
        ];

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Şirket')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('tier.name')
                    ->label('Seviye')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('categories.name')
                    ->label('Kategoriler')
                    ->formatStateUsing(fn ($state, $record) => $record->categories->pluck('name')->implode(', '))
                    ->searchable(),
                TextColumn::make('company_type')
                    ->label('Şirket türü')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('city')
                    ->label('İl')
                    ->sortable(),
                TextColumn::make('district')
                    ->label('İlçe')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                SelectColumn::make('status')
                    ->label('Durum')
                    ->options($statusOptions)
                    ->sortable(),
                TextColumn::make('kyc_status')
                    ->label('KYC')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'info',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'under_review' => 'İnceleniyor',
                        'verified' => 'Doğrulandı',
                        'rejected' => 'Reddedildi',
                        default => $state ?? '-',
                    })
                    ->sortable(),
                TextColumn::make('application_status')
                    ->label('Başvuru')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'under_review' => 'İnceleniyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state ?? '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options($statusOptions),
                SelectFilter::make('kyc_status')
                    ->label('KYC Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'under_review' => 'İnceleniyor',
                        'verified' => 'Doğrulandı',
                        'rejected' => 'Reddedildi',
                    ]),
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
