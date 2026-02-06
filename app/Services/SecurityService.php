<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class SecurityService
{
    /**
     * Kullanıcı girişini kaydet ve şüpheli aktivite kontrolü yap
     */
    public function recordLogin(User $user, Request $request): LoginHistory
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Device ve browser bilgilerini parse et
        $deviceInfo = $this->parseUserAgent($userAgent);
        
        // GeoIP bilgilerini al (basit versiyon, production'da MaxMind GeoIP2 kullanılabilir)
        $geoInfo = $this->getGeoInfo($ipAddress);
        
        // Önceki girişleri kontrol et
        $previousLogins = LoginHistory::where('user_id', $user->id)
            ->where('id', '!=', 0) // İlk giriş değilse
            ->orderBy('logged_in_at', 'desc')
            ->limit(10)
            ->get();
        
        $isNewLocation = $this->isNewLocation($ipAddress, $previousLogins);
        $isNewDevice = $this->isNewDevice($deviceInfo, $previousLogins);
        $isSuspicious = $this->isSuspiciousActivity($ipAddress, $deviceInfo, $previousLogins);
        
        $loginHistory = LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'os' => $deviceInfo['os'],
            'country' => $geoInfo['country'] ?? null,
            'city' => $geoInfo['city'] ?? null,
            'is_suspicious' => $isSuspicious,
            'is_new_location' => $isNewLocation,
            'is_new_device' => $isNewDevice,
            'logged_in_at' => now(),
        ]);
        
        // Şüpheli aktivite veya yeni konum/cihaz varsa email gönder
        if ($isSuspicious || $isNewLocation || $isNewDevice) {
            $this->sendSecurityAlert($user, $loginHistory, [
                'is_suspicious' => $isSuspicious,
                'is_new_location' => $isNewLocation,
                'is_new_device' => $isNewDevice,
            ]);
        }
        
        return $loginHistory;
    }
    
    /**
     * User agent'dan device bilgilerini parse et
     */
    private function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'os' => 'unknown',
            ];
        }
        
        $deviceType = 'desktop';
        $browser = 'unknown';
        $os = 'unknown';
        
        // Device type
        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            $deviceType = 'tablet';
        }
        
        // Browser
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edg|opr/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr/i', $userAgent)) {
            $browser = 'Opera';
        }
        
        // OS
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }
    
    /**
     * IP adresinden geo bilgilerini al (basit versiyon)
     * Production'da MaxMind GeoIP2 veya ipapi.co gibi servisler kullanılabilir
     */
    private function getGeoInfo(string $ipAddress): array
    {
        // Localhost veya private IP'ler için
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1' || str_starts_with($ipAddress, '192.168.') || str_starts_with($ipAddress, '10.')) {
            return ['country' => 'TR', 'city' => 'Local'];
        }
        
        // Basit bir API çağrısı (production'da daha güvenilir servis kullanılmalı)
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ipAddress}?fields=status,country,countryCode,city");
            if ($response) {
                $data = json_decode($response, true);
                if ($data && isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'country' => $data['countryCode'] ?? null,
                        'city' => $data['city'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeoIP lookup failed for {$ipAddress}: " . $e->getMessage());
        }
        
        return ['country' => null, 'city' => null];
    }
    
    /**
     * Yeni konum kontrolü
     */
    private function isNewLocation(string $ipAddress, $previousLogins): bool
    {
        if ($previousLogins->isEmpty()) {
            return false; // İlk giriş, yeni konum sayılmaz
        }
        
        // Son 30 gün içindeki girişlerde bu IP var mı?
        $recentLogins = $previousLogins->filter(function ($login) {
            return $login->logged_in_at->isAfter(now()->subDays(30));
        });
        
        return $recentLogins->where('ip_address', $ipAddress)->isEmpty();
    }
    
    /**
     * Yeni cihaz kontrolü
     */
    private function isNewDevice(array $deviceInfo, $previousLogins): bool
    {
        if ($previousLogins->isEmpty()) {
            return false; // İlk giriş, yeni cihaz sayılmaz
        }
        
        // Son 30 gün içindeki girişlerde bu device var mı?
        $recentLogins = $previousLogins->filter(function ($login) {
            return $login->logged_in_at->isAfter(now()->subDays(30));
        });
        
        $deviceFingerprint = $deviceInfo['device_type'] . '|' . $deviceInfo['browser'] . '|' . $deviceInfo['os'];
        
        foreach ($recentLogins as $login) {
            $loginFingerprint = $login->device_type . '|' . $login->browser . '|' . $login->os;
            if ($loginFingerprint === $deviceFingerprint) {
                return false; // Bu device daha önce kullanılmış
            }
        }
        
        return true;
    }
    
    /**
     * Şüpheli aktivite kontrolü
     */
    private function isSuspiciousActivity(string $ipAddress, array $deviceInfo, $previousLogins): bool
    {
        // 1. Çok hızlı ardışık girişler (5 dakika içinde farklı IP'lerden)
        if ($previousLogins->isNotEmpty()) {
            $lastLogin = $previousLogins->first();
            if ($lastLogin->logged_in_at->isAfter(now()->subMinutes(5)) && $lastLogin->ip_address !== $ipAddress) {
                return true;
            }
        }
        
        // 2. Tor veya VPN IP'leri (basit kontrol, production'da daha gelişmiş servisler kullanılabilir)
        // Burada sadece örnek, gerçek implementasyon için özel servisler gerekli
        
        // 3. Bilinen kötü IP'ler (blacklist kontrolü - production'da database'de tutulabilir)
        
        return false;
    }
    
    /**
     * Güvenlik uyarısı email'i gönder
     */
    private function sendSecurityAlert(User $user, LoginHistory $loginHistory, array $flags): void
    {
        try {
            $subject = 'Güvenlik Uyarısı: Yeni Giriş Tespit Edildi';
            
            $reasons = [];
            if ($flags['is_new_location']) {
                $reasons[] = 'Yeni bir konumdan giriş yapıldı';
            }
            if ($flags['is_new_device']) {
                $reasons[] = 'Yeni bir cihazdan giriş yapıldı';
            }
            if ($flags['is_suspicious']) {
                $reasons[] = 'Şüpheli aktivite tespit edildi';
            }
            
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Hesabınıza yeni bir giriş tespit edildi:\n\n";
            $message .= "Tarih: " . $loginHistory->logged_in_at->format('d.m.Y H:i') . "\n";
            $message .= "IP Adresi: {$loginHistory->ip_address}\n";
            $message .= "Konum: " . ($loginHistory->city ? "{$loginHistory->city}, " : '') . ($loginHistory->country ?? 'Bilinmiyor') . "\n";
            $message .= "Cihaz: {$loginHistory->device_type} - {$loginHistory->browser} ({$loginHistory->os})\n\n";
            
            if (!empty($reasons)) {
                $message .= "Uyarı Sebepleri:\n";
                foreach ($reasons as $reason) {
                    $message .= "• {$reason}\n";
                }
                $message .= "\n";
            }
            
            $message .= "Eğer bu girişi siz yapmadıysanız, lütfen hemen şifrenizi değiştirin ve bizimle iletişime geçin.\n\n";
            $message .= "Bu girişi siz yaptıysanız, bu mesajı görmezden gelebilirsiniz.\n\n";
            $message .= "Saygılarımızla,\nSirizen Güvenlik Ekibi";
            
            Resend::emails()->send([
                'from' => config('mail.from.address', 'noreply@sirizen.com'),
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);
            
            Log::info("Security alert sent to user {$user->id} for login from {$loginHistory->ip_address}");
        } catch (\Exception $e) {
            Log::error("Failed to send security alert to user {$user->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Kullanıcının aktif oturumlarını listele
     */
    public function getActiveSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return LoginHistory::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->orderBy('logged_in_at', 'desc')
            ->get();
    }
    
    /**
     * Belirli bir oturumu sonlandır
     */
    public function logoutSession(int $sessionId, User $user): bool
    {
        $session = LoginHistory::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->first();
        
        if ($session) {
            $session->update(['logged_out_at' => now()]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Tüm oturumları sonlandır (şifre değişikliği sonrası)
     */
    public function logoutAllSessions(User $user, ?int $exceptSessionId = null): void
    {
        $query = LoginHistory::where('user_id', $user->id)
            ->whereNull('logged_out_at');
        
        if ($exceptSessionId) {
            $query->where('id', '!=', $exceptSessionId);
        }
        
        $query->update(['logged_out_at' => now()]);
    }
}
