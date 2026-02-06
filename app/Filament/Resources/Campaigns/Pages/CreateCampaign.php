<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('Before create - Campaign data:', $data);

        if (isset($data['banner'])) {
            Log::info('Banner value:', ['banner' => $data['banner'], 'type' => gettype($data['banner'])]);

            // Check if file exists
            if (Storage::disk('r2')->exists($data['banner'])) {
                Log::info('Banner EXISTS on R2');
            } else {
                Log::warning('Banner NOT found on R2');

                // Check other disks
                if (Storage::disk('local')->exists($data['banner'])) {
                    Log::info('Banner found on LOCAL, moving to R2...');
                    try {
                        $contents = Storage::disk('local')->get($data['banner']);
                        Storage::disk('r2')->put($data['banner'], $contents, 'public');
                        Storage::disk('local')->delete($data['banner']);
                        Log::info('Successfully moved banner to R2');
                    } catch (\Exception $e) {
                        Log::error('Failed to move banner to R2: '.$e->getMessage());
                    }
                }
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Log::info('After create - Campaign created with ID: '.$this->record->id);
    }
}
