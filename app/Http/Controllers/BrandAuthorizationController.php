<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BrandAuthorizationController extends Controller
{
    public function index(): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        // Marka sahibi olduğu markalar
        $ownedBrands = Brand::where('vendor_id', $vendor->id)
            ->with(['authorizedVendors' => function ($query) {
                $query->withPivot([
                    'authorization_type',
                    'authorization_document',
                    'invoice_document',
                    'valid_from',
                    'valid_until',
                    'status',
                ]);
            }])
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => (string) $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo,
                    'authorized_vendors' => $brand->authorizedVendors->map(function ($vendor) {
                        return [
                            'id' => (string) $vendor->id,
                            'name' => $vendor->name,
                            'authorization_type' => $vendor->pivot->authorization_type,
                            'status' => $vendor->pivot->status,
                            'valid_from' => $vendor->pivot->valid_from,
                            'valid_until' => $vendor->pivot->valid_until,
                        ];
                    }),
                ];
            });

        // Yetkili olduğu markalar
        $authorizedBrands = $vendor->authorizedBrands()
            ->withPivot([
                'authorization_type',
                'authorization_document',
                'invoice_document',
                'valid_from',
                'valid_until',
                'status',
            ])
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => (string) $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo,
                    'authorization_type' => $brand->pivot->authorization_type,
                    'status' => $brand->pivot->status,
                    'valid_from' => $brand->pivot->valid_from,
                    'valid_until' => $brand->pivot->valid_until,
                ];
            });

        // Tüm markalar (yetkilendirme için)
        $allBrands = Brand::orderBy('name')->get()->map(function ($brand) {
            return [
                'id' => (string) $brand->id,
                'name' => $brand->name,
            ];
        });

        // Tüm satıcılar (yetkilendirme için)
        $allVendors = Vendor::where('id', '!=', $vendor->id)
            ->orderBy('name')
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => (string) $vendor->id,
                    'name' => $vendor->name,
                ];
            });

        return Inertia::render('brand-authorization/index', [
            'ownedBrands' => $ownedBrands,
            'authorizedBrands' => $authorizedBrands,
            'allBrands' => $allBrands,
            'allVendors' => $allVendors,
        ]);
    }

    public function authorizeVendor(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'vendor_id' => 'required|exists:vendors,id',
            'authorization_type' => 'required|in:owner,authorized_dealer,invoice_chain',
            'authorization_document' => 'nullable|string',
            'invoice_document' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        // Marka sahibi kontrolü
        $brand = Brand::findOrFail($validated['brand_id']);
        if ($brand->vendor_id !== $vendor->id) {
            return back()->withErrors(['brand_id' => 'Bu markanın sahibi değilsiniz.']);
        }

        // Yetkilendirme oluştur
        DB::table('brand_vendor')->updateOrInsert(
            [
                'brand_id' => $validated['brand_id'],
                'vendor_id' => $validated['vendor_id'],
            ],
            [
                'authorization_type' => $validated['authorization_type'],
                'authorization_document' => $validated['authorization_document'] ?? null,
                'invoice_document' => $validated['invoice_document'] ?? null,
                'valid_from' => $validated['valid_from'] ?? now(),
                'valid_until' => $validated['valid_until'] ?? null,
                'status' => 'pending', // Admin onayı gerekebilir
                'is_authorized' => true,
                'authorized_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('brand-authorization.index')
            ->with('success', 'Yetkilendirme başarıyla oluşturuldu. Admin onayı bekleniyor.');
    }

    public function revokeAuthorization(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        // Marka sahibi kontrolü
        $brand = Brand::findOrFail($validated['brand_id']);
        if ($brand->vendor_id !== $vendor->id) {
            return back()->withErrors(['brand_id' => 'Bu markanın sahibi değilsiniz.']);
        }

        // Yetkilendirmeyi iptal et
        DB::table('brand_vendor')
            ->where('brand_id', $validated['brand_id'])
            ->where('vendor_id', $validated['vendor_id'])
            ->update([
                'status' => 'rejected',
                'is_authorized' => false,
                'updated_at' => now(),
            ]);

        return redirect()->route('brand-authorization.index')
            ->with('success', 'Yetkilendirme iptal edildi.');
    }
}
