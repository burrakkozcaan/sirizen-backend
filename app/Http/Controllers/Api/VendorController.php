<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerReview;
use App\Models\Vendor;
use App\Models\VendorFollower;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /**
     * Get all vendors.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vendor::query()->where('status', 'active');

        // Filter by official stores
        if ($request->boolean('is_official')) {
            $query->where('is_official', true);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'followers');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['followers', 'rating', 'product_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->input('per_page', 24), 100);
        $vendors = $query->paginate($perPage);

        // Map followers to follower_count for frontend compatibility
        $vendors->getCollection()->transform(function ($vendor) {
            $vendorArray = $vendor->toArray();
            if (isset($vendorArray['followers'])) {
                $vendorArray['follower_count'] = $vendorArray['followers'];
            }
            return $vendorArray;
        });

        return response()->json($vendors);
    }

    /**
     * Get single vendor by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $vendor = Vendor::where('slug', $slug)
                ->where('status', 'active')
                ->firstOrFail();

            $vendorArray = $vendor->toArray();
            // Map followers to follower_count for frontend compatibility
            if (isset($vendorArray['followers'])) {
                $vendorArray['follower_count'] = $vendorArray['followers'];
            }

            // Include seller page data if exists (load separately to avoid eager loading issues)
            try {
                $sellerPage = $vendor->sellerPages()->first();
                if ($sellerPage) {
                    $vendorArray['seller_page'] = [
                        'id' => $sellerPage->id,
                        'seo_slug' => $sellerPage->seo_slug,
                        'description' => $sellerPage->description,
                        'logo' => $sellerPage->logo,
                        'banner' => $sellerPage->banner,
                    ];
                }
            } catch (\Exception $e) {
                // If seller page relationship fails, just continue without it
                Log::warning("Failed to load seller page for vendor {$vendor->id}: " . $e->getMessage());
            }

            return response()->json($vendorArray);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vendor bulunamadı.',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching vendor {$slug}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Vendor bilgileri alınırken bir hata oluştu.',
            ], 500);
        }
    }

    /**
     * Get vendor's products.
     */
    public function products(string $slug, Request $request): JsonResponse
    {
        $vendor = Vendor::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $query = $vendor->products()
            ->with(['category', 'brand', 'images'])
            ->where('status', 'active');

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['created_at', 'price', 'rating', 'sales_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->input('per_page', 24), 100);
        $page = $request->input('page', 1);
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Get vendor reviews.
     */
    public function reviews(int $id, Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $reviews = SellerReview::where('vendor_id', $id)
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Follow a vendor.
     */
    public function follow(string $slug, Request $request): JsonResponse
    {
        $vendor = Vendor::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $user = $request->user();

        // Check if already following
        $existing = VendorFollower::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Bu mağazayı zaten takip ediyorsunuz.',
                'is_following' => true,
            ], 400);
        }

        // Create follow relationship
        VendorFollower::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);

        // Update vendor follower count
        $vendor->increment('followers'); // Database column is 'followers'

        return response()->json([
            'message' => 'Mağaza takip edildi.',
            'is_following' => true,
        ]);
    }

    /**
     * Unfollow a vendor.
     */
    public function unfollow(string $slug, Request $request): JsonResponse
    {
        $vendor = Vendor::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $user = $request->user();

        $follower = VendorFollower::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$follower) {
            return response()->json([
                'message' => 'Bu mağazayı takip etmiyorsunuz.',
                'is_following' => false,
            ], 400);
        }

        $follower->delete();

        // Update vendor follower count
        $vendor->decrement('followers'); // Database column is 'followers'

        return response()->json([
            'message' => 'Mağaza takipten çıkarıldı.',
            'is_following' => false,
        ]);
    }

    /**
     * Check if user is following a vendor.
     */
    public function checkFollow(string $slug, Request $request): JsonResponse
    {
        $vendor = Vendor::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $user = $request->user();

        $isFollowing = VendorFollower::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        return response()->json([
            'is_following' => $isFollowing,
        ]);
    }

    /**
     * Get featured/popular vendors for homepage carousel.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 50);

        $vendors = Vendor::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('is_official', true)
                    ->orWhere('followers', '>=', 100);
            })
            ->orderByDesc('is_official')
            ->orderByDesc('followers')
            ->orderByDesc('rating')
            ->limit($limit)
            ->get()
            ->map(function ($vendor) {
                $vendorArray = $vendor->toArray();
                if (isset($vendorArray['followers'])) {
                    $vendorArray['follower_count'] = $vendorArray['followers'];
                }
                return $vendorArray;
            });

        return response()->json(['data' => $vendors]);
    }

    /**
     * Get user's followed vendors.
     */
    public function followed(Request $request): JsonResponse
    {
        $user = $request->user();

        $followedVendors = VendorFollower::where('user_id', $user->id)
            ->with(['vendor' => function ($query) {
                $query->where('status', 'active')
                    ->select('id', 'name', 'slug', 'logo', 'rating', 'followers', 'is_official', 'product_count', 'review_count');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('vendor')
            ->filter()
            ->map(function ($vendor) {
                $vendorArray = $vendor->toArray();
                // Map followers to follower_count for frontend compatibility
                if (isset($vendorArray['followers'])) {
                    $vendorArray['follower_count'] = $vendorArray['followers'];
                }
                return $vendorArray;
            });

        return response()->json([
            'data' => $followedVendors->values(),
        ]);
    }
}
