<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm')
                ->label('Onayla')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function (): void {
                    $this->record->update(['status' => 'confirmed']);
                    $this->refreshFormData(['status']);
                }),

            Action::make('process')
                ->label('Hazırlığa Al')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'confirmed')
                ->action(function (): void {
                    $this->record->update(['status' => 'processing']);
                    $this->refreshFormData(['status']);
                }),

            Action::make('ship')
                ->label('Kargoya Ver')
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'processing')
                ->action(function (): void {
                    $this->record->update(['status' => 'shipped']);
                    $this->refreshFormData(['status']);
                }),

            Action::make('deliver')
                ->label('Teslim Edildi')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'shipped')
                ->action(function (): void {
                    $this->record->update(['status' => 'delivered']);
                    $this->refreshFormData(['status']);
                }),

            Action::make('cancel')
                ->label('İptal Et')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Siparişi İptal Et')
                ->modalDescription('Bu siparişi iptal etmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                ->visible(fn () => in_array($this->record->status, ['pending', 'confirmed']))
                ->action(function (): void {
                    $this->record->update(['status' => 'cancelled']);
                    $this->refreshFormData(['status']);
                }),

            DeleteAction::make(),
        ];
    }
}
