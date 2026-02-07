<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchTag;
use Illuminate\Http\JsonResponse;

class SearchTagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = SearchTag::where('is_active', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $tags,
        ]);
    }
}
