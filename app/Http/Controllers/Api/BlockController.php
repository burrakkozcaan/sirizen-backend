<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlockService;
use Illuminate\Http\JsonResponse;

class BlockController extends Controller
{
    public function __construct(
        private BlockService $service
    ) {}

    /**
     * GET /api/pdp-blocks/{productId}
     * Get blocks for a product (grouped by position)
     */
    public function show(int $productId): JsonResponse
    {
        $blocks = $this->service->getBlocksForProduct($productId);

        return response()->json([
            'data' => $blocks,
        ])->header('Cache-Control', 'public, max-age=21600'); // 6 hours
    }
}
