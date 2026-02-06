<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * GET /api/locations/cities
     * Get all cities
     */
    public function cities(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        
        $cities = City::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'plate_code']);

        return response()->json([
            'data' => $cities,
        ]);
    }

    /**
     * GET /api/locations/cities/{cityId}/districts
     * Get districts by city
     */
    public function districts(int $cityId, Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        
        $districts = District::where('city_id', $cityId)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'city_id']);

        return response()->json([
            'data' => $districts,
        ]);
    }

    /**
     * GET /api/locations/estimate
     * Get delivery estimate for city/district
     * Query params: city_id, district_id, product_id (optional)
     */
    public function estimate(Request $request): JsonResponse
    {
        $cityId = $request->integer('city_id');
        $districtId = $request->integer('district_id');
        $productId = $request->integer('product_id');
        
        // Default values
        $dispatchDays = 1; // Default dispatch time
        $shippingDays = 2; // Default shipping time
        $shippingType = 'normal'; // normal, express, same_day
        
        // If product_id is provided, get actual dispatch_days and shipping_type from product seller
        if ($productId) {
            $product = \App\Models\Product::find($productId);
            if ($product && $product->vendor_id) {
                $productSeller = \App\Models\ProductSeller::where('product_id', $productId)
                    ->where('vendor_id', $product->vendor_id)
                    ->first();
                
                if ($productSeller) {
                    $dispatchDays = $productSeller->dispatch_days ?? 1;
                    $shippingType = $productSeller->shipping_type ?? 'normal';
                    
                    // Adjust shipping days based on shipping type
                    switch ($shippingType) {
                        case 'same_day':
                            $shippingDays = 1;
                            break;
                        case 'express':
                            $shippingDays = 1;
                            break;
                        case 'normal':
                        default:
                            $shippingDays = 2;
                            break;
                    }
                }
            }
        }
        
        // Calculate total business days (dispatch + shipping)
        $totalBusinessDays = $dispatchDays + $shippingDays;
        
        // Calculate estimated delivery date (skip weekends)
        $estimatedDate = now();
        $daysAdded = 0;
        while ($daysAdded < $totalBusinessDays) {
            $estimatedDate->addDay();
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($estimatedDate->dayOfWeek !== 6 && $estimatedDate->dayOfWeek !== 0) {
                $daysAdded++;
            }
        }
        
        return response()->json([
            'data' => [
                'estimated_delivery_date' => $estimatedDate->format('Y-m-d'),
                'business_days' => $totalBusinessDays,
                'dispatch_days' => $dispatchDays,
                'shipping_days' => $shippingDays,
                'shipping_type' => $shippingType,
                'formatted_date' => $estimatedDate->locale('tr')->isoFormat('D MMMM dddd'),
            ],
        ]);
    }
}
