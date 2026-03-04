<?php

namespace App\Filament\Resources\Commissions\Pages;

use App\CommissionStatus;
use App\Filament\Resources\Commissions\CommissionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommission extends EditRecord
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pay')
                ->label('Öde')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === CommissionStatus::PENDING)
                ->action(function (): void {
                    $this->record->update(['status' => CommissionStatus::PAID]);
                    $this->refreshFormData(['status']);
                }),

            Action::make('settle')
                ->label('Kesinleştir')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === CommissionStatus::PAID)
                ->action(function (): void {
                    $this->record->update([
                        'status' => CommissionStatus::SETTLED,
                        'settled_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'settled_at']);
                }),

            DeleteAction::make(),
        ];
    }
}
