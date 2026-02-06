<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct(
        private PricingService $service
    ) {}

    /**
     * GET /api/pricing/{productId}?variant={variantId}
     * Get product pricing (short cache: 30 seconds)
     */
    public function show(int $productId, Request $request): JsonResponse
    {
        $variantId = $request->integer('variant');
        $pricing = $this->service->getPricing($productId, $variantId ?: null);

        if (!$pricing) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'data' => $pricing,
        ])->header('Cache-Control', 'public, max-age=30'); // 30 seconds
    }
}

