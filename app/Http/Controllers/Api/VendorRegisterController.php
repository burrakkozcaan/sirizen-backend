<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorTier;
use App\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class VendorRegisterController extends Controller
{
    /**
     * Satıcı kaydı oluştur
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],

            // Vendor bilgileri
            'company_name' => ['required', 'string', 'max:255'],
            'company_type' => ['required', 'string', 'in:individual,limited,joint_stock'],
            'tax_number' => ['required', 'string', 'max:50'],
            'business_license_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],

            // Adres bilgileri
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string'],

            // Kategoriler
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],

            // İletişim
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();

            // 1. User oluştur - ROL SATICI OLARAK AYARLA
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => UserRole::VENDOR, // ÖNEMLİ: Satıcı rolü
                'email_verified' => false,
            ]);

            // 2. Default tier bul veya oluştur
            $defaultTier = VendorTier::firstOrCreate(
                ['name' => 'Başlangıç'],
                [
                    'commission_rate' => 15.00,
                    'max_products' => 100,
                    'description' => 'Yeni satıcılar için başlangıç seviyesi',
                ]
            );

            // 3. Vendor kaydı oluştur
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'tier_id' => $defaultTier->id,
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'description' => $validated['description'] ?? null,
                'company_type' => $validated['company_type'],
                'tax_number' => $validated['tax_number'],
                'business_license_number' => $validated['business_license_number'] ?? null,
                'iban' => $validated['iban'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'account_holder_name' => $validated['account_holder_name'] ?? null,
                'city' => $validated['city'],
                'district' => $validated['district'] ?? null,
                'address' => $validated['address'],
                'status' => 'pending', // Başlangıç durumu: onay bekliyor
                'kyc_status' => 'pending', // KYC onay bekliyor
                'application_status' => 'pending', // Başvuru beklemede
                'application_submitted_at' => now(),
                'rating' => 0,
                'total_orders' => 0,
                'followers' => 0,
            ]);

            // 4. Kategorileri ilişkilendir (varsa)
            if (!empty($validated['categories'])) {
                $vendor->categories()->attach($validated['categories']);
            }

            DB::commit();

            // 5. Token oluştur
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Satıcı başvurunuz alındı! Onay sürecinden sonra bilgilendirileceksiniz.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->value,
                    ],
                    'vendor' => [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'status' => $vendor->status,
                        'application_status' => $vendor->application_status,
                    ],
                    'token' => $token,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Kayıt sırasında bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
