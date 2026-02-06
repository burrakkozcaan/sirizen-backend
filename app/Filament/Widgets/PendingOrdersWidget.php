<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Kargoya Verilecek Siparişler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user', 'items.productSeller.product'])
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Ürün')
                    ->counts('items')
                    ->suffix(' adet'),

                TextColumn::make('total_price')
                    ->label('Tutar')
                    ->money('TRY')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'processing' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Onay Bekliyor',
                        'processing' => 'Hazırlanıyor',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Sipariş Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->description(fn (Order $record): string => $record->created_at->diffForHumans()),
            ])
            ->actions([
                Action::make('view')
                    ->label('Detay')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record)),
            ])
            ->emptyStateHeading('Harika!')
            ->emptyStateDescription('Bekleyen sipariş bulunmuyor.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultSort('created_at', 'asc')
            ->paginated(false);
    }
}
