<?php

namespace App\Filament\Resources\Payments\Pages;

use App\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Services\Payment\PaymentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        $refundableStatuses = [PaymentStatus::Completed, PaymentStatus::PartiallyRefunded];

        return [
            Action::make('full_refund')
                ->label('Tam İade')
                ->icon('heroicon-o-receipt-refund')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tam İade')
                ->modalDescription('Ödemenin tamamı iade edilecek. Bu işlem geri alınamaz.')
                ->visible(fn () => in_array($this->record->status, $refundableStatuses))
                ->action(function (): void {
                    $service = app(PaymentService::class);
                    $result = $service->processRefund($this->record);

                    if ($result['success']) {
                        Notification::make()->title('Tam iade başarılı')->success()->send();
                        $this->refreshFormData(['status', 'refunded_amount', 'refunded_at']);
                    } else {
                        Notification::make()->title('İade başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'))->danger()->send();
                    }
                }),

            Action::make('partial_refund')
                ->label('Kısmi İade')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, $refundableStatuses))
                ->form([
                    TextInput::make('amount')
                        ->label('İade Tutarı (₺)')
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->maxValue(fn () => (float) $this->record->amount - (float) ($this->record->refunded_amount ?? 0))
                        ->helperText(fn () => 'Maksimum: ₺' . number_format((float) $this->record->amount - (float) ($this->record->refunded_amount ?? 0), 2)),
                ])
                ->action(function (array $data): void {
                    $service = app(PaymentService::class);
                    $result = $service->processRefund($this->record, (float) $data['amount']);

                    if ($result['success']) {
                        Notification::make()->title('Kısmi iade başarılı')->success()->send();
                        $this->refreshFormData(['status', 'refunded_amount', 'refunded_at']);
                    } else {
                        Notification::make()->title('İade başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'))->danger()->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
