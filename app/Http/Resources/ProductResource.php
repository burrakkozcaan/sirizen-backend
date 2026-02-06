<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductSellerResource;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
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

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $sellers = $this->whenLoaded('productSellers', fn () => ProductSellerResource::collection($this->productSellers));
        $fastestSeller = $this->whenLoaded('productSellers', function () {
            return $this->productSellers->sortBy('dispatch_days')->first();
        });
        $district = $this->resolveDistrict($request);

        $data['product_sellers'] = $sellers;
        $data['fastest_seller_id'] = ($fastestSeller && !($fastestSeller instanceof \Illuminate\Http\Resources\MissingValue)) ? $fastestSeller->id : null;
        $data['district_extra_delivery_days'] = $district?->extra_delivery_days ?? 0;

        return $data;
    }
}
