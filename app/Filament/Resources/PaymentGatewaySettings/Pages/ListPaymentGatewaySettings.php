<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentGatewaySettings extends ListRecords
{
    protected static string $resource = PaymentGatewaySettingResource::class;
}
