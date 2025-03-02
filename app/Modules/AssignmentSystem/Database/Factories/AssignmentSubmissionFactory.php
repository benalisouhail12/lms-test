<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\AssignmentSubmission;

use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentSubmissionFactory extends Factory
{
    protected $model = AssignmentSubmission::class;

    public function definition()
    {
        return [
            'assignment_id' => AssignmentFactory::new()->create()->id,
            'user_id' => UserFactory::new()->create()->id,
            'group_id' => AssignmentGroupFactory::new()->create()->id,
            'submission_text' => $this->faker->paragraph,
            'submitted_files' => [$this->faker->fileExtension, $this->faker->fileExtension],
            'attempt_number' => $this->faker->numberBetween(1, 3),
            'status' => $this->faker->randomElement(['submitted', 'graded', 'pending']),
            'submitted_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'is_late' => $this->faker->boolean,
            'similarity_score' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
