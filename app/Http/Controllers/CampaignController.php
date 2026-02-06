<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $campaigns = Campaign::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => (string) $campaign->id,
                    'title' => (string) $campaign->title,
                    'slug' => (string) $campaign->slug,
                    'description' => (string) ($campaign->description ?? ''),
                    'discount_type' => (string) $campaign->discount_type,
                    'discount_value' => (string) $campaign->discount_value,
                    'starts_at' => $campaign->starts_at?->toIso8601String() ?? '',
                    'ends_at' => $campaign->ends_at?->toIso8601String() ?? '',
                    'is_active' => (bool) $campaign->is_active,
                    'created_at' => $campaign->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('campaign/index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $slug = \Illuminate\Support\Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Campaign::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        Campaign::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('campaign.index')
            ->with('success', 'Kampanya başarıyla oluşturuldu.');
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $campaign = Campaign::findOrFail($id);

        // Vendor kontrolü
        if ($campaign->vendor_id !== $vendor->id) {
            abort(403, 'Bu kampanyaya erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaign.index')
            ->with('success', 'Kampanya başarıyla güncellendi.');
    }
}
