<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Generate 6-digit verification code
        $verificationCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(5); // Code expires in 5 minutes

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => UserRole::CUSTOMER,
            'email_verified' => false,
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => $expiresAt,
        ]);

        // Send verification code email
        try {
            $subject = 'E-posta Doğrulama Kodu - Sirizen';
            $message = "Merhaba {$user->name},\n\n";
            $message .= "Sirizen'e hoş geldiniz! E-posta adresinizi doğrulamak için aşağıdaki kodu kullanın:\n\n";
            $message .= "Doğrulama Kodu: {$verificationCode}\n\n";
            $message .= "Bu kod 5 dakika geçerlidir.\n\n";
            $message .= "Eğer bu işlemi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            $fromAddress = config('mail.from.address');
            
            // Resend için domain doğrulaması kontrolü
            if (str_contains($fromAddress, '@example.com') || str_contains($fromAddress, '@localhost')) {
                Log::warning("Resend domain not verified. Using fallback: onboarding@resend.dev");
                $fromAddress = 'onboarding@resend.dev'; // Resend'in test domain'i
            }

            Resend::emails()->send([
                'from' => $fromAddress,
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info("Verification code sent to {$user->email} for user {$user->id}");
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error("Failed to send verification code email to {$user->email}: {$errorMessage}");
            
            // Domain doğrulama hatası kontrolü - fallback ile tekrar dene
            if (str_contains($errorMessage, 'domain is not verified') || str_contains($errorMessage, 'Domain not verified')) {
                Log::warning("Resend domain verification required. Trying fallback: onboarding@resend.dev");
                try {
                    Resend::emails()->send([
                        'from' => 'onboarding@resend.dev',
                        'to' => $user->email,
                        'subject' => $subject,
                        'text' => $message,
                    ]);
                    Log::info("Verification code sent using test domain to {$user->email}");
                } catch (\Exception $fallbackError) {
                    Log::error("Fallback email send also failed: " . $fallbackError->getMessage());
                    Log::warning("Please verify your domain in Resend dashboard: https://resend.com/domains");
                }
            }
            // Continue even if email fails - code is still stored
        }

        return response()->json([
            'message' => 'Kayıt başarılı! E-posta adresinize doğrulama kodu gönderildi.',
            'email' => $user->email,
            'requires_verification' => true,
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email veya şifre hatalı.',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Update last login
        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Giriş başarılı!',
            'user' => new UserResource($user),
            'token' => $token,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Get authenticated user info.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Çıkış başarılı!',
        ]);
    }

    /**
     * Verify email with code.
     */
    public function verifyEmailCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Kullanıcı bulunamadı.',
            ], 404);
        }

        // Check if code matches
        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'message' => 'Doğrulama kodu hatalı.',
            ], 400);
        }

        // Check if code is expired
        if ($user->email_verification_code_expires_at && $user->email_verification_code_expires_at->isPast()) {
            return response()->json([
                'message' => 'Doğrulama kodu süresi dolmuş. Lütfen yeni bir kod isteyin.',
            ], 400);
        }

        // Verify email
        $user->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ]);

        // Create token for verified user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'E-posta başarıyla doğrulandı!',
            'user' => new UserResource($user),
            'token' => $token,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Resend verification code.
     */
    public function resendVerificationCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Kullanıcı bulunamadı.',
            ], 404);
        }

        // Check if already verified
        if ($user->email_verified) {
            return response()->json([
                'message' => 'E-posta adresi zaten doğrulanmış.',
            ], 400);
        }

        // Generate new 6-digit verification code
        $verificationCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(5);

        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => $expiresAt,
        ]);

        // Send verification code email
        try {
            $subject = 'E-posta Doğrulama Kodu - Sirizen';
            $message = "Merhaba {$user->name},\n\n";
            $message .= "E-posta adresinizi doğrulamak için aşağıdaki kodu kullanın:\n\n";
            $message .= "Doğrulama Kodu: {$verificationCode}\n\n";
            $message .= "Bu kod 5 dakika geçerlidir.\n\n";
            $message .= "Eğer bu işlemi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.\n\n";
            $message .= "Saygılarımızla,\nSirizen Ekibi";

            $fromAddress = config('mail.from.address');
            
            // Resend için domain doğrulaması kontrolü
            // Eğer example.com veya doğrulanmamış domain kullanılıyorsa, Resend'in test domain'ini kullan
            if (str_contains($fromAddress, '@example.com') || str_contains($fromAddress, '@localhost') || empty($fromAddress)) {
                Log::warning("Resend domain not verified. Using test domain: onboarding@resend.dev");
                $fromAddress = 'onboarding@resend.dev'; // Resend'in test domain'i
            }

            Resend::emails()->send([
                'from' => $fromAddress,
                'to' => $user->email,
                'subject' => $subject,
                'text' => $message,
            ]);

            Log::info("Verification code resent to {$user->email} for user {$user->id}");

            return response()->json([
                'message' => 'Doğrulama kodu tekrar gönderildi.',
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error("Failed to resend verification code email to {$user->email}: {$errorMessage}");
            
            // Domain doğrulama hatası kontrolü - fallback ile tekrar dene
            if (str_contains($errorMessage, 'domain is not verified') || str_contains($errorMessage, 'Domain not verified')) {
                try {
                    Resend::emails()->send([
                        'from' => 'onboarding@resend.dev',
                        'to' => $user->email,
                        'subject' => $subject,
                        'text' => $message,
                    ]);
                    Log::info("Verification code resent using test domain to {$user->email}");
                    return response()->json([
                        'message' => 'Doğrulama kodu tekrar gönderildi.',
                    ]);
                } catch (\Exception $fallbackError) {
                    Log::error("Fallback email send also failed: " . $fallbackError->getMessage());
                    return response()->json([
                        'message' => 'E-posta gönderilemedi. Lütfen Resend dashboard\'unda domain doğrulaması yapın: https://resend.com/domains',
                    ], 500);
                }
            }
            
            return response()->json([
                'message' => 'E-posta gönderilemedi. Lütfen tekrar deneyin.',
            ], 500);
        }
    }
}
