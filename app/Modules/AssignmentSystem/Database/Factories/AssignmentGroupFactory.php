<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\AssignmentGroup;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentGroupFactory extends Factory
{
    protected $model = AssignmentGroup::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'assignment_id' => AssignmentFactory::new()->create()->id,
        ];
    }
}
