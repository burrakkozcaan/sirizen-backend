<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\CargoIntegration;
use App\Models\Shipment;

interface CargoProviderInterface
{
    /**
     * Kargo gönderisi oluştur
     *
     * @return array{success: bool, tracking_number?: string, cargo_reference_id?: string, label_url?: string, barcode_url?: string, error?: string}
     */
    public function createShipment(Shipment $shipment, CargoIntegration $integration): array;

    /**
     * Kargo takip sorgula
     *
     * @return array{success: bool, status?: string, events?: array<int, array{date: string, status: string, location?: string, description?: string}>, error?: string}
     */
    public function trackShipment(string $trackingNumber, CargoIntegration $integration): array;

    /**
     * Kargo iptal
     *
     * @return array{success: bool, error?: string}
     */
    public function cancelShipment(string $trackingNumber, CargoIntegration $integration): array;

    /**
     * Barkod/etiket al
     *
     * @return array{success: bool, label_url?: string, label_content?: string, barcode_url?: string, error?: string}
     */
    public function getLabel(string $trackingNumber, CargoIntegration $integration): array;

    /**
     * Kargo ücreti hesapla
     *
     * @return array{success: bool, price?: float, currency?: string, error?: string}
     */
    public function calculateRate(float $weight, float $desi, CargoIntegration $integration): array;

    /**
     * Provider kodu döndür
     */
    public function getProviderCode(): string;
}
