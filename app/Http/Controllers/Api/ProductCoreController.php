<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductCoreService;
use Illuminate\Http\JsonResponse;

class ProductCoreController extends Controller
{
    public function __construct(
        private ProductCoreService $service
    ) {}

    /**
     * GET /api/product-core/{id}
     * Get product core data (long cache: 1 day)
     */
    public function show(string $id): JsonResponse
    {
        $isSlug = !is_numeric($id);
        $core = $this->service->getProductCore($id, $isSlug);

        if (!$core) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'data' => $core,
        ])->header('Cache-Control', 'public, max-age=86400'); // 1 day
    }
}

