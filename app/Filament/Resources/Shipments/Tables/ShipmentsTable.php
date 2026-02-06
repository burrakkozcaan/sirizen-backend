<?php

namespace App\Filament\Resources\Shipments\Tables;

use App\Services\Cargo\CargoService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orderItem.product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('vendor.name')
                    ->label('Satıcı')
                    ->searchable(),
                TextColumn::make('shippingCompany.name')
                    ->label('Kargo Firması')
                    ->searchable(),
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => $record->tracking_url, shouldOpenInNewTab: true)
                    ->color('primary'),
                TextColumn::make('cargo_reference_id')
                    ->label('Referans ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Beklemede',
                        'created' => 'Oluşturuldu',
                        'picked_up' => 'Alındı',
                        'in_transit' => 'Yolda',
                        'out_for_delivery' => 'Dağıtımda',
                        'delivered' => 'Teslim Edildi',
                        'returned' => 'İade',
                        'cancelled' => 'İptal',
                        'failed' => 'Başarısız',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'gray',
                        'created', 'picked_up' => 'info',
                        'in_transit' => 'primary',
                        'out_for_delivery' => 'warning',
                        'delivered' => 'success',
                        'returned', 'cancelled', 'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('current_location')
                    ->label('Mevcut Konum')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('progress_percent')
                    ->label('İlerleme')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('shipped_at')
                    ->label('Kargoya Verildi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('estimated_delivery')
                    ->label('Tahmini Teslimat')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                TextColumn::make('delivered_at')
                    ->label('Teslim Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('last_tracking_update')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'created' => 'Oluşturuldu',
                        'picked_up' => 'Alındı',
                        'in_transit' => 'Yolda',
                        'out_for_delivery' => 'Dağıtımda',
                        'delivered' => 'Teslim Edildi',
                        'returned' => 'İade',
                        'cancelled' => 'İptal',
                        'failed' => 'Başarısız',
                    ]),
                SelectFilter::make('shipping_company_id')
                    ->label('Kargo Firması')
                    ->relationship('shippingCompany', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('track')
                    ->label('Takip Sorgula')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('info')
                    ->visible(fn ($record) => $record->tracking_number && $record->isActive())
                    ->action(function ($record) {
                        $cargoService = app(CargoService::class);
                        $result = $cargoService->trackShipment($record);

                        if ($result['success']) {
                            Notification::make()
                                ->title('Takip Güncellendi')
                                ->body("Durum: " . ($result['status'] ?? $record->status))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Takip Hatası')
                                ->body($result['error'] ?? 'Takip bilgisi alınamadı')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('label')
                    ->label('Etiket')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->visible(fn ($record) => $record->tracking_number)
                    ->action(function ($record) {
                        if ($record->barcode_url) {
                            return redirect($record->barcode_url);
                        }

                        $cargoService = app(CargoService::class);
                        $result = $cargoService->getLabel($record);

                        if ($result['success'] && ($result['barcode_url'] ?? $result['label_url'])) {
                            Notification::make()
                                ->title('Etiket Alındı')
                                ->success()
                                ->send();

                            return redirect($result['barcode_url'] ?? $result['label_url']);
                        }

                        Notification::make()
                            ->title('Etiket Hatası')
                            ->body($result['error'] ?? 'Etiket alınamadı')
                            ->danger()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
