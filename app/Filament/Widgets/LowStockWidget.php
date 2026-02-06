<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected static ?string $heading = 'Stok Uyarıları';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with(['product.images'])
                    ->where('stock', '<=', 5)
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'asc')
                    ->limit(8)
            )
            ->columns([
                ImageColumn::make('product.images')
                    ->label('')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->getStateUsing(fn ($record) => $record->product?->images?->first()?->url),

                TextColumn::make('product.title')
                    ->label('Ürün')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->product?->title),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->size('xs')
                    ->color('gray'),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
            ])
            ->emptyStateHeading('Stok durumu iyi')
            ->emptyStateDescription('Düşük stoklu ürün yok.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
