<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationSetting>
 */
class NotificationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email_campaigns' => $this->faker->boolean(),
            'email_orders' => $this->faker->boolean(),
            'email_promotions' => $this->faker->boolean(),
            'email_reviews' => $this->faker->boolean(),
            'sms_campaigns' => $this->faker->boolean(),
            'sms_orders' => $this->faker->boolean(),
            'sms_promotions' => $this->faker->boolean(),
            'push_enabled' => $this->faker->boolean(),
            'push_campaigns' => $this->faker->boolean(),
            'push_orders' => $this->faker->boolean(),
            'push_messages' => $this->faker->boolean(),
        ];
    }
}
