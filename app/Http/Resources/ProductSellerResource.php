<?php

namespace App\Http\Resources;

use App\Http\Resources\VendorResource;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSellerResource extends JsonResource
{
    /**
     * Cache resolved districts per request to avoid duplicate queries.
     */
    protected static array $districtCache = [];

    protected function resolveDistrict(Request $request): ?District
    {
        $districtId = $request->query('district_id');

        if (! $districtId) {
            return null;
        }

        if (! array_key_exists($districtId, self::$districtCache)) {
            self::$districtCache[$districtId] = District::find($districtId);
        }

        return self::$districtCache[$districtId];
    }

    public function toArray(Request $request): array
    {
        $district = $this->resolveDistrict($request);
        $extraDays = $district?->extra_delivery_days ?? 0;
        $etaDays = $this->dispatch_days + $extraDays;

        return [
            'id' => $this->id,
            'vendor' => new VendorResource($this->whenLoaded('vendor') ?? $this->vendor),
            'price' => $this->price,
            'stock' => $this->stock,
            'dispatch_days' => $this->dispatch_days,
            'shipping_type' => $this->shipping_type,
            'is_featured' => $this->is_featured,
            'estimated_delivery_days' => $etaDays,
            'estimated_delivery_date' => now()->addDays($etaDays)->toDateString(),
            'district_extra_delivery_days' => $extraDays,
        ];
    }
}
