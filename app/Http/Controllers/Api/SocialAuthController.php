<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Google ile giriş (Frontend'den gelen token ile)
     *
     * Frontend Google OAuth'dan token alır, buraya gönderir.
     * Biz token'ı doğrulayıp user oluşturur/login yaparız.
     */
    public function googleCallback(Request $request): JsonResponse
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        try {
            $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
            
            if (!$clientId) {
                Log::error('Google Client ID not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Google Client ID yapılandırılmamış',
                ], 500);
            }

            // Google token'ı doğrula
            // Önce Google\AccessToken\Verify kullanmayı dene, yoksa manuel JWT decode
            $payload = null;
            
            if (class_exists('\Google\AccessToken\Verify')) {
                try {
                    $verify = new \Google\AccessToken\Verify();
                    $payload = $verify->verifyIdToken($request->credential, $clientId);
                } catch (\Exception $e) {
                    Log::warning('Google\AccessToken\Verify failed, trying manual verification: ' . $e->getMessage());
                }
            }
            
            // Eğer Google\AccessToken\Verify yoksa veya başarısız olduysa, manuel JWT decode dene
            if (!$payload) {
                $payload = $this->verifyGoogleIdToken($request->credential, $clientId);
            }

            if (!$payload) {
                Log::warning('Invalid Google token', ['credential_length' => strlen($request->credential)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz Google token',
                ], 401);
            }

            $googleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? '';
            $avatar = $payload['picture'] ?? null;
            $emailVerified = $payload['email_verified'] ?? false;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google hesabınızdan e-posta bilgisi alınamadı',
                ], 400);
            }

            // User'ı bul veya oluştur
            $user = User::where('email', $email)->first();

            if ($user) {
                // Mevcut user - google_id yoksa ekle ve email verified güncelle
                $updateData = [];
                if (!$user->google_id && $googleId) {
                    $updateData['google_id'] = $googleId;
                }
                if (!$user->email_verified_at && $emailVerified) {
                    $updateData['email_verified_at'] = now();
                    $updateData['email_verified'] = true;
                }
                if ($avatar && !$user->avatar) {
                    $updateData['avatar'] = $avatar;
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            } else {
                // Yeni user oluştur
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => $emailVerified ? now() : null,
                    'email_verified' => $emailVerified,
                    'role' => UserRole::CUSTOMER,
                ]);
            }

            // Vendor kontrolü - eğer vendor ise status kontrolü yap
            if ($user->isVendor()) {
                if (!$user->email_verified_at) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Giriş yapmak için e-postanızı doğrulamanız gerekmektedir.',
                    ], 403);
                }

                if (!$user->vendor || $user->vendor->status !== 'active') {
                    $statusMessage = 'Başvurunuz inceleniyor. Onay sonrası giriş yapabilirsiniz.';
                    
                    if ($user->vendor && $user->vendor->status === 'rejected') {
                        $statusMessage = 'Başvurunuz reddedilmiştir. Detaylar için lütfen bizimle iletişime geçin.';
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => $statusMessage,
                    ], 403);
                }
            }

            // Token oluştur
            $token = $user->createToken('google-auth')->plainTextToken;

            // Login tracking (güvenlik için)
            try {
                $securityService = app(\App\Services\SecurityService::class);
                $securityService->recordLogin($user, $request);
            } catch (\Exception $e) {
                Log::warning('Login tracking failed for Google login: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Giriş başarılı',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'role' => $user->role->value,
                    ],
                    'token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Google ile giriş başarısız: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Google ID token'ı manuel olarak doğrula (JWT decode)
     * Google API Client yoksa bu yöntem kullanılır
     */
    private function verifyGoogleIdToken(string $idToken, string $clientId): ?array
    {
        try {
            // JWT token'ı decode et (basit versiyon - production'da daha güvenli yöntemler kullanılmalı)
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                return null;
            }

            // Payload'ı decode et
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            
            if (!$payload) {
                return null;
            }

            // Temel validasyonlar
            // 1. Audience (aud) kontrolü
            if (isset($payload['aud']) && $payload['aud'] !== $clientId) {
                Log::warning('Google token audience mismatch', [
                    'expected' => $clientId,
                    'received' => $payload['aud'] ?? null,
                ]);
                return null;
            }

            // 2. Expiration (exp) kontrolü
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                Log::warning('Google token expired');
                return null;
            }

            // 3. Issuer (iss) kontrolü
            $validIssuers = [
                'accounts.google.com',
                'https://accounts.google.com',
            ];
            if (isset($payload['iss']) && !in_array($payload['iss'], $validIssuers)) {
                Log::warning('Google token invalid issuer', ['iss' => $payload['iss']]);
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            Log::error('Manual Google token verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Apple ile giriş (ileride eklenebilir)
     */
    public function appleCallback(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Apple ile giriş henüz desteklenmiyor',
        ], 501);
    }
}
