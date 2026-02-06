<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCampaignController extends Controller
{
    public function __construct(
        private CampaignService $service
    ) {}

    /**
     * GET /api/campaigns?productId={productId}
     * Get campaigns for product (user-based cache: 10 minutes)
     */
    public function index(Request $request): JsonResponse
    {
        $productId = $request->integer('productId');
        $userId = $request->user()?->id;

        if (!$productId) {
            return response()->json(['message' => 'productId is required'], 400);
        }

        $campaigns = $this->service->getCampaigns($productId, $userId);

        return response()->json([
            'data' => $campaigns,
        ])->header('Cache-Control', 'private, max-age=600'); // 10 minutes, private for user-based
    }
}

