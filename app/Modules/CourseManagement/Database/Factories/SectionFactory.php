<?php

namespace app\Modules\CourseManagement\Database\Factories;

use App\Modules\CourseManagement\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'position' => $this->faker->numberBetween(1, 100),
            'course_id' => CourseFactory::new(), // Assumes you have a Course factory
            'is_published' => $this->faker->boolean(),
        ];
    }
}
