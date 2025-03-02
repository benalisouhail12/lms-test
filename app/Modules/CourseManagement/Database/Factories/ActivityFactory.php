<?php

namespace app\Modules\CourseManagement\Database\Factories;

use app\Modules\CourseManagement\Database\Factories\LessonFactory;
use App\Modules\CourseManagement\Models\Activity;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'activity_type' => $this->faker->randomElement(['QUIZ', 'ASSIGNMENT', 'DISCUSSION']),
            'lesson_id' => LessonFactory::new()->create()->id,
            'is_required' => $this->faker->boolean,
            'points' => $this->faker->numberBetween(1, 100),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
