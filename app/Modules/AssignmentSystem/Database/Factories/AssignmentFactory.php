<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\Assignment;
use app\Modules\CourseManagement\Database\Factories\ActivityFactory;
use app\Modules\CourseManagement\Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'instructions' => $this->faker->text,
            'course_id' => CourseFactory::new()->create()->id,
            'activity_id' => ActivityFactory::new()->create()->id,
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'available_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'max_points' => $this->faker->numberBetween(10, 100),
            'passing_grade' => $this->faker->numberBetween(5, 50),
            'allow_late_submissions' => $this->faker->boolean,
            'late_submission_penalty' => $this->faker->randomFloat(2, 0, 10),
            'enable_plagiarism_detection' => $this->faker->boolean,
            'allowed_file_types' => ['pdf', 'docx', 'txt'],
            'max_file_size' => $this->faker->numberBetween(1, 10), // MB
            'max_attempts' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['draft', 'published', 'closed']),
            'is_group_assignment' => $this->faker->boolean,
        ];
    }
}
