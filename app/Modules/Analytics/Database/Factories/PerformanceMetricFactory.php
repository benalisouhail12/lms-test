<?php

namespace app\Modules\Analytics\Database\Factories;

use App\Modules\Analytics\Models\PerformanceMetric;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class PerformanceMetricFactory extends Factory
{
    protected $model = PerformanceMetric::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'value' => $this->faker->randomFloat(2, 0, 100),
            'previous_value' => $this->faker->randomFloat(2, 0, 100),
            'unit' => $this->faker->word,
            'period' => $this->faker->word,
            'date_recorded' => $this->faker->dateTimeThisYear(),
        ];
    }
}
