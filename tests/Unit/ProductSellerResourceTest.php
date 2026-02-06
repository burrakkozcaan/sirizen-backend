<?php

use App\Http\Resources\ProductSellerResource;
use App\Models\District;
use App\Models\ProductSeller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

abstract class AbstractProductSellerResourceStub extends ProductSellerResource
{
    public function __construct(ProductSeller $resource, protected ?District $district)
    {
        parent::__construct($resource);
    }

    protected function resolveDistrict(Request $request): ?District
    {
        return $this->district;
    }
}

it('calculates estimated delivery using product dispatch days', function () {
    $seller = new ProductSeller([
        'id' => 1,
        'product_id' => 1,
        'vendor_id' => 1,
        'price' => 1299.99,
        'stock' => 20,
        'dispatch_days' => 2,
        'shipping_type' => 'normal',
        'is_featured' => false,
    ]);
    $seller->setRelation('vendor', Vendor::make(['name' => 'Test Seller']));

    $resource = new class($seller, null) extends AbstractProductSellerResourceStub {};

    $payload = $resource->toArray(Request::create('/'));

    expect($payload['estimated_delivery_days'])->toBe(2)
        ->and($payload['estimated_delivery_date'])->toBe(now()->addDays(2)->toDateString())
        ->and($payload['district_extra_delivery_days'])->toBe(0);
});

it('adds district extra days to estimated delivery', function () {
    $seller = new ProductSeller([
        'id' => 2,
        'product_id' => 1,
        'vendor_id' => 1,
        'price' => 2499.00,
        'stock' => 15,
        'dispatch_days' => 1,
        'shipping_type' => 'express',
        'is_featured' => false,
    ]);
    $seller->setRelation('vendor', Vendor::make(['name' => 'Test Seller']));

    $district = District::make(['extra_delivery_days' => 3]);
    $resource = new class($seller, $district) extends AbstractProductSellerResourceStub {};

    $payload = $resource->toArray(Request::create('/', 'GET', ['district_id' => 42]));

    expect($payload['estimated_delivery_days'])->toBe(4)
        ->and($payload['estimated_delivery_date'])->toBe(now()->addDays(4)->toDateString())
        ->and($payload['district_extra_delivery_days'])->toBe(3);
});
