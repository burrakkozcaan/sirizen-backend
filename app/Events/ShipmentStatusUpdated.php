<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Shipment $shipment,
        public string $previousStatus,
        public string $newStatus
    ) {}
}
