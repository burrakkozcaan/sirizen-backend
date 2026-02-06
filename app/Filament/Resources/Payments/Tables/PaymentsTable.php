<?php

namespace App\Filament\Resources\Payments\Tables;

use App\PaymentProvider;
use App\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('order.order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendors')
                    ->label('Satıcı')
                    ->getStateUsing(function ($record) {
                        $vendors = $record->order?->items
                            ?->map(fn ($item) => $item->vendor?->name)
                            ->filter()
                            ->unique()
                            ->values();

                        if (! $vendors || $vendors->isEmpty()) {
                            return '-';
                        }

                        return $vendors->implode(', ');
                    })
                    ->wrap(),
                TextColumn::make('payment_provider')
                    ->label('Gateway')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentProvider ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof PaymentProvider ? $state->color() : 'gray'),
                TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Komisyon')
                    ->money('TRY')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('vendor_amount')
                    ->label('Satıcı Payı')
                    ->money('TRY')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('platform_amount')
                    ->label('Platform Payı')
                    ->money('TRY')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('installment_count')
                    ->label('Taksit')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} Taksit" : 'Tek Çekim')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof PaymentStatus ? $state->color() : 'gray')
                    ->sortable(),
                TextColumn::make('split_status')
                    ->label('Dağılım')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'settled' => 'Dağıtıldı',
                        'pending' => 'Bekliyor',
                        default => $state ?? '-',
                    })
                    ->color(fn ($state) => match ($state) {
                        'settled' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('paid_at')
                    ->label('Ödeme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])),
                SelectFilter::make('payment_provider')
                    ->label('Gateway')
                    ->options(collect(PaymentProvider::cases())->mapWithKeys(fn ($provider) => [$provider->value => $provider->label()])),
                SelectFilter::make('split_status')
                    ->label('Dağılım Durumu')
                    ->options([
                        'settled' => 'Dağıtıldı',
                        'pending' => 'Bekliyor',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
