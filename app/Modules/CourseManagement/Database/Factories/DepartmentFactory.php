<?php

namespace  app\Modules\CourseManagement\Database\Factories;

use App\Modules\CourseManagement\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

  class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company(),
            'code' => strtoupper($this->faker->unique()->lexify('???')), // Random 3-letter code
            'description' => $this->faker->sentence(),
        ];
    }
}
