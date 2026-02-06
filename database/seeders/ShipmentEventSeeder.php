<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Database\Seeder;

class ShipmentEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shipmentIds = Shipment::query()->pluck('id');

        if ($shipmentIds->isEmpty()) {
            $shipmentIds = Shipment::factory()->count(3)->create()->pluck('id');
        }

        foreach ($shipmentIds->take(3) as $shipmentId) {
            ShipmentEvent::factory()->create([
                'shipment_id' => $shipmentId,
            ]);
        }
    }
}
