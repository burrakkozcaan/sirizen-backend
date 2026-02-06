<?php

namespace App\Http\Controllers;

use App\Models\SellerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SellerPageController extends Controller
{
    /**
     * Display the seller page for the vendor.
     */
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        // Get or create seller page for this vendor
        $sellerPage = SellerPage::firstOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'seo_slug' => Str::slug($vendor->name ?? 'seller-' . $vendor->id),
                'description' => $vendor->description ?? null,
            ]
        );

        // If slug is not unique, make it unique
        if (SellerPage::where('seo_slug', $sellerPage->seo_slug)
            ->where('id', '!=', $sellerPage->id)
            ->exists()) {
            $counter = 1;
            $uniqueSlug = $sellerPage->seo_slug . '-' . $counter;
            while (SellerPage::where('seo_slug', $uniqueSlug)->exists()) {
                $counter++;
                $uniqueSlug = $sellerPage->seo_slug . '-' . $counter;
            }
            $sellerPage->update(['seo_slug' => $uniqueSlug]);
        }

        return Inertia::render('seller-page/index', [
            'sellerPage' => [
                'id' => (string) $sellerPage->id,
                'vendor_id' => (string) $sellerPage->vendor_id,
                'seo_slug' => (string) $sellerPage->seo_slug,
                'description' => $sellerPage->description ? (string) $sellerPage->description : null,
                'banner' => $sellerPage->banner ? Storage::disk('r2')->url($sellerPage->banner) : null,
                'logo' => $sellerPage->logo ? Storage::disk('r2')->url($sellerPage->logo) : null,
                'created_at' => $sellerPage->created_at?->toIso8601String() ?? '',
                'updated_at' => $sellerPage->updated_at?->toIso8601String() ?? '',
            ],
            'vendor' => [
                'id' => (string) $vendor->id,
                'name' => (string) $vendor->name,
                'slug' => (string) $vendor->slug,
            ],
        ]);
    }

    /**
     * Update the seller page.
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $sellerPage = SellerPage::where('vendor_id', $vendor->id)->firstOrFail();

        $validated = $request->validate([
            'seo_slug' => 'required|string|max:255|unique:seller_pages,seo_slug,' . $sellerPage->id,
            'description' => 'nullable|string|max:5000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($sellerPage->logo && Storage::disk('r2')->exists($sellerPage->logo)) {
                Storage::disk('r2')->delete($sellerPage->logo);
            }

            $logoPath = $request->file('logo')->store('sellers/logos', 'r2');
            $validated['logo'] = $logoPath;
        } else {
            unset($validated['logo']);
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($sellerPage->banner && Storage::disk('r2')->exists($sellerPage->banner)) {
                Storage::disk('r2')->delete($sellerPage->banner);
            }

            $bannerPath = $request->file('banner')->store('sellers/banners', 'r2');
            $validated['banner'] = $bannerPath;
        } else {
            unset($validated['banner']);
        }

        $sellerPage->update($validated);

        return redirect()->route('seller-page.index')->with('success', 'Mağaza sayfası başarıyla güncellendi.');
    }
}

