<?php

namespace App\Observers;

use App\Models\SellerPage;
use App\Models\Vendor;
use App\Models\VendorBalance;
use App\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;

class VendorObserver
{
    /**
     * Handle the Vendor "created" event.
     */
    public function created(Vendor $vendor): void
    {
        // Vendor oluşturulduğunda seller page oluştur
        $this->createSellerPage($vendor);

        // VendorBalance oluştur
        VendorBalance::firstOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'balance' => 0,
                'available_balance' => 0,
                'pending_balance' => 0,
                'total_earnings' => 0,
                'total_withdrawn' => 0,
                'currency' => 'TRY',
            ]
        );
    }

    /**
     * Handle the Vendor "updated" event.
     */
    public function updated(Vendor $vendor): void
    {
        // Vendor onaylandığında (status 'active' olduğunda)
        if ($vendor->wasChanged('status') && $vendor->status === 'active') {
            // 1. Kullanıcı rolünü VENDOR yap
            $this->updateUserRoleToVendor($vendor);

            // 2. Onay e-postası gönder
            $this->sendApprovalEmail($vendor);

            // 3. Eğer seller page yoksa oluştur
            if (!$vendor->sellerPages()->exists()) {
                $this->createSellerPage($vendor);
            }

            // 4. VendorBalance yoksa oluştur
            VendorBalance::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => 0,
                    'available_balance' => 0,
                    'pending_balance' => 0,
                    'total_earnings' => 0,
                    'total_withdrawn' => 0,
                    'currency' => 'TRY',
                ]
            );

            Log::info('Vendor approved', [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'user_id' => $vendor->user_id,
            ]);
        }

        // Vendor reddedildiğinde
        if ($vendor->wasChanged('status') && $vendor->status === 'rejected') {
            $this->sendRejectionEmail($vendor);
        }

        // Vendor askıya alındığında
        if ($vendor->wasChanged('status') && $vendor->status === 'suspended') {
            $this->sendSuspensionEmail($vendor);
        }
    }

    /**
     * Kullanıcı rolünü VENDOR olarak güncelle
     */
    protected function updateUserRoleToVendor(Vendor $vendor): void
    {
        $user = $vendor->user;

        if ($user && $user->role !== UserRole::VENDOR) {
            $user->update(['role' => UserRole::VENDOR]);

            Log::info('User role updated to VENDOR', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
    }

    /**
     * Onay e-postası gönder
     */
    protected function sendApprovalEmail(Vendor $vendor): void
    {
        $user = $vendor->user;

        if (!$user || !$user->email) {
            return;
        }

        try {
            $subject = 'Tebrikler! Satıcı Başvurunuz Onaylandı - Sirizen';
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Harika haber! Sirizen satıcı başvurunuz onaylandı. 🎉\n\n";
            $message .= "Mağaza Bilgileri:\n";
            $message .= "- Mağaza Adı: {$vendor->name}\n";
            $message .= "- Satıcı ID: {$vendor->id}\n\n";
            $message .= "Artık ürünlerinizi yüklemeye başlayabilirsiniz.\n\n";
            $message .= "Satıcı Paneli: " . url('/dashboard') . "\n\n";
            $message .= "Başarılar dileriz!\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            $fromAddress = config('mail.from.address');
            if (str_contains($fromAddress, '@example.com') || str_contains($fromAddress, '@localhost')) {
                $fromAddress = 'onboarding@resend.dev';
            }

            Resend::emails()->send([
                'from' => $fromAddress,
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info('Vendor approval email sent', [
                'vendor_id' => $vendor->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send vendor approval email', [
                'vendor_id' => $vendor->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Red e-postası gönder
     */
    protected function sendRejectionEmail(Vendor $vendor): void
    {
        $user = $vendor->user;

        if (!$user || !$user->email) {
            return;
        }

        try {
            $subject = 'Satıcı Başvurunuz Hakkında - Sirizen';
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Satıcı başvurunuzu inceledik.\n\n";
            $message .= "Maalesef başvurunuz şu an için onaylanamamıştır.\n\n";

            if ($vendor->rejection_reason) {
                $message .= "Sebep: {$vendor->rejection_reason}\n\n";
            }

            $message .= "Eksik bilgilerinizi tamamlayarak tekrar başvurabilirsiniz.\n\n";
            $message .= "Sorularınız için bizimle iletişime geçebilirsiniz.\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            $fromAddress = config('mail.from.address');
            if (str_contains($fromAddress, '@example.com') || str_contains($fromAddress, '@localhost')) {
                $fromAddress = 'onboarding@resend.dev';
            }

            Resend::emails()->send([
                'from' => $fromAddress,
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info('Vendor rejection email sent', [
                'vendor_id' => $vendor->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send vendor rejection email', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Askıya alma e-postası gönder
     */
    protected function sendSuspensionEmail(Vendor $vendor): void
    {
        $user = $vendor->user;

        if (!$user || !$user->email) {
            return;
        }

        try {
            $subject = 'Mağazanız Askıya Alındı - Sirizen';
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Mağazanız ({$vendor->name}) geçici olarak askıya alınmıştır.\n\n";
            $message .= "Detaylı bilgi için lütfen bizimle iletişime geçin.\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            $fromAddress = config('mail.from.address');
            if (str_contains($fromAddress, '@example.com') || str_contains($fromAddress, '@localhost')) {
                $fromAddress = 'onboarding@resend.dev';
            }

            Resend::emails()->send([
                'from' => $fromAddress,
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info('Vendor suspension email sent', [
                'vendor_id' => $vendor->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send vendor suspension email', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create seller page for vendor
     */
    protected function createSellerPage(Vendor $vendor): void
    {
        // SEO slug oluştur (vendor slug'ını kullan)
        $seoSlug = $vendor->slug;

        // Eğer aynı slug'da sayfa varsa, unique yap
        $existingPage = SellerPage::where('seo_slug', $seoSlug)->first();
        if ($existingPage) {
            $seoSlug = $vendor->slug . '-' . $vendor->id;
        }

        SellerPage::create([
            'vendor_id' => $vendor->id,
            'seo_slug' => $seoSlug,
            'description' => $vendor->description,
            'logo' => null, // Vendor'dan logo alınabilir
            'banner' => null,
        ]);
    }

    /**
     * Handle the Vendor "deleted" event.
     */
    public function deleted(Vendor $vendor): void
    {
        //
    }

    /**
     * Handle the Vendor "restored" event.
     */
    public function restored(Vendor $vendor): void
    {
        //
    }

    /**
     * Handle the Vendor "force deleted" event.
     */
    public function forceDeleted(Vendor $vendor): void
    {
        //
    }
}
