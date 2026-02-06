<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id');

        if ($userIds->isEmpty()) {
            $userIds = User::factory()->count(5)->create()->pluck('id');
        }

        foreach ($userIds->take(5) as $userId) {
            NotificationSetting::factory()->create([
                'user_id' => $userId,
            ]);
        }
    }
}
