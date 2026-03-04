<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use App\Services\VendorNotificationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected ?string $originalStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kyc_approve')
                ->label('KYC Onayla')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->kyc_status, ['pending', 'under_review']))
                ->action(function (): void {
                    $this->record->update([
                        'kyc_status' => 'verified',
                        'kyc_verified_at' => now(),
                        'kyc_verified_by' => Auth::id(),
                    ]);
                    $this->refreshFormData(['kyc_status', 'kyc_verified_at']);
                    Notification::make()->title('KYC onaylandı')->success()->send();
                }),

            Action::make('kyc_reject')
                ->label('KYC Reddet')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger')
                ->visible(fn () => in_array($this->record->kyc_status, ['pending', 'under_review', 'verified']))
                ->form([
                    Textarea::make('kyc_notes')
                        ->label('Red Nedeni')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'kyc_status' => 'rejected',
                        'kyc_notes' => $data['kyc_notes'],
                    ]);
                    $this->refreshFormData(['kyc_status', 'kyc_notes']);
                    Notification::make()->title('KYC reddedildi')->warning()->send();
                }),

            Action::make('suspend')
                ->label('Askıya Al')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active')
                ->action(function (): void {
                    $this->record->update(['status' => 'suspended']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Mağaza askıya alındı')->warning()->send();
                }),

            Action::make('activate')
                ->label('Aktifleştir')
                ->icon('heroicon-o-play-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'suspended')
                ->action(function (): void {
                    $this->record->update(['status' => 'active']);
                    $this->refreshFormData(['status']);
                    $notificationService = app(VendorNotificationService::class);
                    $notificationService->sendApprovalEmail($this->record);
                    Notification::make()->title('Mağaza aktifleştirildi')->success()->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function beforeFill(): void
    {
        $this->originalStatus = $this->record->status;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['application_reviewed_by'] = Auth::id();
        $data['application_reviewed_at'] = now();

        return $data;
    }

    protected function afterSave(): void
    {
        $vendor = $this->record->fresh();
        $originalStatus = $this->originalStatus;
        $newStatus = $vendor->status;

        if ($originalStatus !== $newStatus) {
            $notificationService = app(VendorNotificationService::class);

            if (in_array($originalStatus, ['pending', 'review', null]) && $newStatus === 'active') {
                $notificationService->sendApprovalEmail($vendor);
            }

            if ($newStatus === 'rejected' && $originalStatus !== 'rejected') {
                $rejectionReason = $vendor->rejection_reason ?? 'Başvurunuz gereksinimleri karşılamamaktadır.';
                $notificationService->sendRejectionEmail($vendor, $rejectionReason);
            }
        }
    }
}
