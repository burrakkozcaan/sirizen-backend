<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use App\Models\Vendor;
use App\Services\VendorNotificationService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected ?string $originalStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeFill(): void
    {
        // Form doldurulmadan önce original status'u sakla
        $this->originalStatus = $this->record->status;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Admin bilgisini ekle
        $data['application_reviewed_by'] = Auth::id();
        $data['application_reviewed_at'] = now();

        return $data;
    }

    protected function afterSave(): void
    {
        $vendor = $this->record->fresh(); // Fresh instance al
        $originalStatus = $this->originalStatus;
        $newStatus = $vendor->status;

        // Status değiştiyse email gönder
        if ($originalStatus !== $newStatus) {
            $notificationService = app(VendorNotificationService::class);

            // Status 'pending', 'review' veya null iken 'active' olduysa onay emaili gönder
            if (in_array($originalStatus, ['pending', 'review', null]) && $newStatus === 'active') {
                $notificationService->sendApprovalEmail($vendor);
            }

            // Status 'rejected' olduysa red emaili gönder
            if ($newStatus === 'rejected' && $originalStatus !== 'rejected') {
                $rejectionReason = $vendor->rejection_reason ?? 'Başvurunuz gereksinimleri karşılamamaktadır.';
                $notificationService->sendRejectionEmail($vendor, $rejectionReason);
            }
        }
    }
}
