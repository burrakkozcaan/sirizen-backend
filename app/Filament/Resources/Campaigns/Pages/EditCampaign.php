<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('Before save - Campaign data:', $data);

        if (isset($data['banner'])) {
            Log::info('Banner value:', ['banner' => $data['banner'], 'type' => gettype($data['banner'])]);

            // Check if file exists on R2
            if (Storage::disk('r2')->exists($data['banner'])) {
                Log::info('Banner EXISTS on R2');
            } else {
                Log::warning('Banner NOT found on R2');

                // Check if it's on local disk
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
                } else {
                    Log::error('Banner NOT found on LOCAL either - file is completely missing!');
                }
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Log::info('After save - Campaign updated with ID: '.$this->record->id);

        if ($this->record->banner) {
            $exists = Storage::disk('r2')->exists($this->record->banner);
            Log::info('Final verification - Banner on R2:', ['exists' => $exists, 'path' => $this->record->banner]);
        }
    }
}
