<?php

namespace app\Modules\Notifications\Database\Factories;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Notifications\Models\NotificationPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition()
    {
        return [
            'user_id' => UserFactory::new()->create()->id, // Assuming you have a User factory
            'channels' => [
                'email' => ['enabled' => $this->faker->boolean],
                'sms' => ['enabled' => $this->faker->boolean],
                'push' => ['enabled' => $this->faker->boolean],
            ],
            'notification_types' => [
                'new_message' => ['enabled' => $this->faker->boolean, 'channels' => ['email', 'push']],
                'new_comment' => ['enabled' => $this->faker->boolean, 'channels' => ['email', 'sms']],
            ],
            'quiet_hours' => [
                'enabled' => $this->faker->boolean,
                'timezone' => $this->faker->timezone,
                'start' => $this->faker->time(),
                'end' => $this->faker->time(),
            ],
            'digest_settings' => [
                'enabled' => $this->faker->boolean,
                'frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            ],
        ];
    }
}
