<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\AssignmentExtension;
use App\Modules\AssignmentSystem\Models\Assignment;
use app\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentExtensionFactory extends Factory
{
    protected $model = AssignmentExtension::class;

    public function definition()
    {
        return [
            'assignment_id' => AssignmentFactory::new()->create()->id,
            'user_id' => UserFactory::new()->create()->id,
            'extended_due_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'reason' => $this->faker->sentence(),
            'granted_by' =>  UserFactory::new()->create()->id,
        ];
    }

    public function withReason(string $reason)
    {
        return $this->state(fn (array $attributes) => [
            'reason' => $reason,
        ]);
    }
}
