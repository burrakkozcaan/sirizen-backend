<?php

namespace App\Filament\Resources\Commissions\Tables;

use App\CommissionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orderItem.order.order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orderItem.order.user.name')
                    ->label('Kullanıcı')
                    ->sortable(),
                TextColumn::make('gross_amount')
                    ->label('Brüt Tutar')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('commission_rate')
                    ->label('Oran')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Komisyon')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label('Net Tutar')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof CommissionStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof CommissionStatus ? $state->color() : 'gray')
                    ->sortable(),
                TextColumn::make('settled_at')
                    ->label('Kesinleşme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('refunded_amount')
                    ->label('İade Tutarı')
                    ->money('TRY')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(collect(CommissionStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('settle')
                        ->label('Kesinleştir (PAID → SETTLED)')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records
                                ->filter(fn ($r) => $r->status === CommissionStatus::PAID)
                                ->each(fn ($r) => $r->update([
                                    'status' => CommissionStatus::SETTLED,
                                    'settled_at' => now(),
                                ]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
