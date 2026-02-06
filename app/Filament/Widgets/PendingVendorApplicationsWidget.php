<?php

namespace App\Filament\Widgets;

use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingVendorApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Bekleyen Satıcı Başvuruları';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vendor::query()
                    ->with(['user'])
                    ->where('application_status', 'pending')
                    ->latest('application_submitted_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Mağaza Adı')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('E-posta')
                    ->copyable()
                    ->size('sm'),

                TextColumn::make('company_type')
                    ->label('Şirket Tipi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->value ?? '-'),

                TextColumn::make('city')
                    ->label('Şehir'),

                TextColumn::make('application_submitted_at')
                    ->label('Başvuru Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->description(fn (Vendor $record): string => $record->application_submitted_at?->diffForHumans() ?? ''),
            ])
            ->actions([
                Action::make('review')
                    ->label('İncele')
                    ->icon('heroicon-m-eye')
                    ->color('warning')
                    ->url(fn (Vendor $record): string => route('filament.admin.resources.vendors.edit', $record)),
            ])
            ->emptyStateHeading('Bekleyen başvuru yok')
            ->emptyStateDescription('Tüm satıcı başvuruları incelendi.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
