<?php

namespace app\Modules\CourseManagement\Database\Factories;

use App\Modules\CourseManagement\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraph(),
            'position' => $this->faker->numberBetween(1, 100),
            'section_id' => SectionFactory::new(), // Creates a section for the lesson
            'estimated_duration' => $this->faker->numberBetween(10, 120), // Duration in minutes
            'is_published' => $this->faker->boolean(),
            'lesson_type' => $this->faker->randomElement(['TEXT', 'VIDEO', 'QUIZ', 'ASSIGNMENT']),
        ];
    }
}
