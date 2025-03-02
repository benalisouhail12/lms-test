<?php

namespace  app\Modules\CourseManagement\Database\Factories;

use App\Modules\CourseManagement\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'learning_objectives' => json_encode([$this->faker->sentence(), $this->faker->sentence()]),
            'course_type' => $this->faker->randomElement(['ONLINE', 'HYBRID', 'IN_PERSON']),
            'status' => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'ARCHIVED']),
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+6 months'),
            'level' => $this->faker->randomElement(['BEGINNER', 'INTERMEDIATE', 'ADVANCED']),
            'duration_in_weeks' => $this->faker->numberBetween(4, 16),
            'credit_hours' => $this->faker->numberBetween(1, 6),
            'capacity' => $this->faker->numberBetween(10, 100),
            'department_id' => DepartmentFactory::new(),
            'program_id' => ProgramFactory::new(),
        ];
    }
}

