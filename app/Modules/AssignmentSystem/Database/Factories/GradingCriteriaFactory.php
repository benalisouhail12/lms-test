<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\GradingCriteria;
use App\Modules\AssignmentSystem\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradingCriteriaFactory extends Factory
{
    protected $model = GradingCriteria::class;

    public function definition()
    {
        return [
            'assignment_id' => AssignmentFactory::new()->create()->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'max_points' => $this->faker->numberBetween(10, 100),
            'weight' => $this->faker->randomFloat(2, 0.1, 1.0),
        ];
    }

    public function withWeight(float $weight)
    {
        return $this->state(fn (array $attributes) => [
            'weight' => $weight,
        ]);
    }
}
