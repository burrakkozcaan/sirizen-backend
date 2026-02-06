<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class VendorNotificationService
{
    /**
     * Vendor başvurusu onaylandığında email gönder
     */
    public function sendApprovalEmail(Vendor $vendor): void
    {
        try {
            $user = $vendor->user;
            
            if (!$user || !$user->email) {
                Log::warning("Vendor {$vendor->id} için email gönderilemedi: kullanıcı veya email bulunamadı");
                return;
            }

            $subject = 'Başvurunuz Onaylandı - Sirizen Satıcı Paneli';
            
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Sirizen satıcı başvurunuz başarıyla onaylanmıştır!\n\n";
            $message .= "Satıcı Bilgileri:\n";
            $message .= "• Şirket Adı: {$vendor->name}\n";
            $message .= "• Durum: Aktif\n\n";
            
            if ($vendor->application_reviewed_at) {
                $message .= "Onay Tarihi: " . $vendor->application_reviewed_at->format('d.m.Y H:i') . "\n\n";
            }
            
            $message .= "Artık Sirizen satıcı paneline giriş yaparak ürünlerinizi ekleyebilir ve satışa başlayabilirsiniz.\n\n";
            $message .= "Giriş yapmak için: " . config('app.url') . "/vendor/login\n\n";
            $message .= "Sorularınız için bizimle iletişime geçebilirsiniz.\n\n";
            $message .= "Başarılar dileriz,\nSirizen Ekibi";

            Resend::emails()->send([
                'from' => config('mail.from.address', 'noreply@sirizen.com'),
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info("Vendor approval email sent to {$user->email} for vendor {$vendor->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send vendor approval email to vendor {$vendor->id}: " . $e->getMessage());
        }
    }

    /**
     * Vendor başvurusu reddedildiğinde email gönder
     */
    public function sendRejectionEmail(Vendor $vendor, ?string $reason = null): void
    {
        try {
            $user = $vendor->user;
            
            if (!$user || !$user->email) {
                Log::warning("Vendor {$vendor->id} için email gönderilemedi: kullanıcı veya email bulunamadı");
                return;
            }

            $subject = 'Başvurunuz Hakkında - Sirizen';
            
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Maalesef Sirizen satıcı başvurunuz şu an için onaylanamamıştır.\n\n";
            
            if ($reason) {
                $message .= "Red Nedeni:\n{$reason}\n\n";
            }
            
            $message .= "Başvurunuzu tekrar gözden geçirmek veya eksik bilgileri tamamlamak için lütfen bizimle iletişime geçin.\n\n";
            $message .= "Sorularınız için: " . config('mail.from.address', 'destek@sirizen.com') . "\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            Resend::emails()->send([
                'from' => config('mail.from.address', 'noreply@sirizen.com'),
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info("Vendor rejection email sent to {$user->email} for vendor {$vendor->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send vendor rejection email to vendor {$vendor->id}: " . $e->getMessage());
        }
    }

    /**
     * Vendor başvurusu yapıldığında email gönder
     */
    public function sendApplicationSubmittedEmail(Vendor $vendor): void
    {
        try {
            $user = $vendor->user;
            
            if (!$user || !$user->email) {
                Log::warning("Vendor {$vendor->id} için email gönderilemedi: kullanıcı veya email bulunamadı");
                return;
            }

            $subject = 'Başvurunuz Alındı - Sirizen Satıcı Paneli';
            
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Sirizen satıcı başvurunuz başarıyla alınmıştır.\n\n";
            $message .= "Başvuru Bilgileri:\n";
            $message .= "• Şirket Adı: {$vendor->name}\n";
            $message .= "• Durum: İnceleme Aşamasında\n\n";
            
            if ($vendor->application_submitted_at) {
                $message .= "Başvuru Tarihi: " . $vendor->application_submitted_at->format('d.m.Y H:i') . "\n\n";
            }
            
            $message .= "Başvurunuz incelendikten sonra size e-posta ile bilgi verilecektir.\n";
            $message .= "İnceleme süreci genellikle 1-3 iş günü sürmektedir.\n\n";
            $message .= "Sorularınız için: " . config('mail.from.address', 'destek@sirizen.com') . "\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            Resend::emails()->send([
                'from' => config('mail.from.address', 'noreply@sirizen.com'),
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info("Vendor application submitted email sent to {$user->email} for vendor {$vendor->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send vendor application submitted email to vendor {$vendor->id}: " . $e->getMessage());
        }
    }
}
