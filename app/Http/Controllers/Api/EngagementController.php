<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EngagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EngagementController extends Controller
{
    public function __construct(
        private EngagementService $service
    ) {}

    /**
     * GET /api/engagement/{productId}
     * Get engagement stats (very short cache: 30 seconds)
     */
    public function show(int $productId, Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $engagement = $this->service->getEngagement($productId, $userId);

        if (!$engagement) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'data' => $engagement,
        ])->header('Cache-Control', 'public, max-age=30'); // 30 seconds
    }
}

