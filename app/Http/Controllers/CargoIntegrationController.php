<?php

namespace App\Http\Controllers;

use App\Models\CargoIntegration;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CargoIntegrationController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $integrations = CargoIntegration::where('vendor_id', $vendor->id)
            ->with('shippingCompany')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($integration) {
                return [
                    'id' => (string) $integration->id,
                    'integration_type' => (string) $integration->integration_type,
                    'shipping_company_name' => $integration->shippingCompany ? (string) $integration->shippingCompany->name : null,
                    'customer_code' => $integration->customer_code ? (string) $integration->customer_code : null,
                    'is_active' => (bool) $integration->is_active,
                    'is_test_mode' => (bool) $integration->is_test_mode,
                    'last_sync_at' => $integration->last_sync_at?->toIso8601String() ?? null,
                    'last_error' => $integration->last_error ? (string) $integration->last_error : null,
                    'created_at' => $integration->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        $shippingCompanies = ShippingCompany::orderBy('name')->get()->map(function ($company) {
            return [
                'id' => (string) $company->id,
                'name' => (string) $company->name,
            ];
        })->values()->all();

        return Inertia::render('cargo-integration/index', [
            'integrations' => $integrations,
            'shippingCompanies' => $shippingCompanies,
        ]);
    }
}

