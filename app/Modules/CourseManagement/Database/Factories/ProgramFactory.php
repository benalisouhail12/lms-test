<?php

namespace app\Modules\CourseManagement\Database\Factories;

use App\Modules\CourseManagement\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'department_id' => DepartmentFactory::new(), // Create a department factory record for the foreign key
        ];
    }
}
