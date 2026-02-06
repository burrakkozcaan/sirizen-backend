<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected static ?string $heading = 'Son Siparişler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->weight('bold')
                    ->size('sm'),

                TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->size('sm'),

                TextColumn::make('total_price')
                    ->label('Tutar')
                    ->money('TRY')
                    ->weight('bold')
                    ->size('sm'),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'processing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                        default => $state,
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record)),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
