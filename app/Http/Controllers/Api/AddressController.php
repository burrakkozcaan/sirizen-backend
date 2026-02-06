<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get user's addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $addresses]);
    }

    /**
     * Create new address.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line' => 'required|string',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'sometimes|boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'title' => $validated['title'],
            'full_name' => $validated['full_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address_line' => $validated['address_line'],
            'city' => $validated['city'],
            'district' => $validated['district'],
            'neighborhood' => $validated['neighborhood'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'is_default' => $validated['is_default'] ?? false,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Adres oluşturuldu.',
            'data' => $address,
        ], 201);
    }

    /**
     * Update address.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title' => 'string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line' => 'string',
            'city' => 'string|max:100',
            'district' => 'string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'title' => $request->input('title', $address->title),
            'full_name' => $request->input('full_name', $address->full_name),
            'phone' => $request->input('phone', $address->phone),
            'address_line' => $request->input('address_line', $address->address_line),
            'city' => $request->input('city', $address->city),
            'district' => $request->input('district', $address->district),
            'neighborhood' => $request->input('neighborhood', $address->neighborhood),
            'postal_code' => $request->input('postal_code', $address->postal_code),
            'is_default' => $request->boolean('is_default', $address->is_default),
        ]);

        return response()->json([
            'message' => 'Adres güncellendi.',
            'data' => $address,
        ]);
    }

    /**
     * Set address as default.
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Unset all other defaults
        Address::where('user_id', $request->user()->id)
            ->where('id', '!=', $id)
            ->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Varsayılan adres güncellendi.',
            'data' => $address,
        ]);
    }

    /**
     * Delete address.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->delete();

        return response()->json([
            'message' => 'Adres silindi.',
        ]);
    }
}
