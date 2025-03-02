<?php

namespace app\Modules\Analytics\Database\Factories;

use app\Modules\Analytics\Models\Report;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'metrics' => $this->faker->randomElements(['metric_1', 'metric_2', 'metric_3', 'metric_4'], 2),
            'period' => $this->faker->date('Y-m-d'),
            'data' => $this->faker->randomElements(['data_point_1', 'data_point_2', 'data_point_3'], 3),
            'created_by' =>UserFactory::new()->create()->id, // Assuming User factory is already defined
        ];
    }
}
