<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $addresses = Address::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => (string) $address->id,
                    'title' => (string) ($address->title ?? ''),
                    'address_line' => (string) ($address->address_line ?? ''),
                    'address' => (string) ($address->address_line ?? ''), // Backward compatibility
                    'city' => (string) ($address->city ?? ''),
                    'district' => (string) ($address->district ?? ''),
                    'neighborhood' => $address->neighborhood ? (string) $address->neighborhood : null,
                    'postal_code' => (string) ($address->postal_code ?? ''),
                    'phone' => (string) ($address->phone ?? ''),
                    'full_name' => $address->full_name ? (string) $address->full_name : null,
                    'address_type' => $address->address_type ? (string) $address->address_type : null,
                    'is_default' => (bool) ($address->is_default ?? false),
                    'created_at' => $address->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('address/index', [
            'addresses' => $addresses,
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
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line' => 'required|string',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address_type' => 'nullable|string|in:home,work,billing,shipping',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Address::where('vendor_id', $vendor->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'full_name' => $validated['full_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address_line' => $validated['address_line'],
            'city' => $validated['city'],
            'district' => $validated['district'],
            'neighborhood' => $validated['neighborhood'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'address_type' => $validated['address_type'] ?? 'home',
            'is_default' => $validated['is_default'] ?? false,
        ]);

        return redirect()->route('address.index')
            ->with('success', 'Adres başarıyla eklendi.');
    }
}
