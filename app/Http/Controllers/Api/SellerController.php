<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SellerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function __construct(
        private SellerService $service
    ) {}

    /**
     * GET /api/seller/{sellerId}
     * Get seller info (medium cache: 5 minutes)
     */
    public function show(int $sellerId, Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $seller = $this->service->getSeller($sellerId, $userId);

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        return response()->json([
            'data' => $seller,
        ])->header('Cache-Control', 'public, max-age=300'); // 5 minutes
    }
}

