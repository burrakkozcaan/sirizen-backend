<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuickLink;
use Illuminate\Http\JsonResponse;

class QuickLinkController extends Controller
{
    /**
     * Get quick links for homepage.
     */
    public function index(): JsonResponse
    {
        $links = QuickLink::where('is_active', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $links,
        ]);
    }
}
