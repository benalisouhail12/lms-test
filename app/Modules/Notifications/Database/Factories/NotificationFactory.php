<?php

namespace app\Modules\Notifications\Database\Factories;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => UserFactory::new()->create()->id, // Assuming you have a User factory
            'type' => $this->faker->word,
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'meta_data' => $this->faker->randomElement([null, $this->faker->word]),
            'status' => $this->faker->randomElement(['unread', 'read']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'expires_at' => $this->faker->optional()->dateTime,
            'group_id' => $this->faker->randomNumber(),
        ];
    }
}
