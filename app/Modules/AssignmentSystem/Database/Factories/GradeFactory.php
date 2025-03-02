<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use App\Modules\AssignmentSystem\Models\Grade;
use App\Modules\AssignmentSystem\Models\AssignmentSubmission;
use app\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition()
    {
        $pointsPossible = $this->faker->numberBetween(50, 100);
        $pointsEarned = $this->faker->numberBetween(0, $pointsPossible);
        $percentage = ($pointsPossible > 0) ? ($pointsEarned / $pointsPossible) * 100 : 0;

        return [
            'assignment_submission_id' => AssignmentSubmissionFactory::new()->create()->id,
            'points_earned' => $pointsEarned,
            'points_possible' => $pointsPossible,
            'percentage' => $percentage,
            'letter_grade' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'F']),
            'graded_by' => UserFactory::new()->create()->id,
            'graded_at' => now(),
            'is_final' => $this->faker->boolean(),
        ];
    }

    public function finalGrade()
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => true,
        ]);
    }

    public function withLetterGrade(string $letter)
    {
        return $this->state(fn (array $attributes) => [
            'letter_grade' => $letter,
        ]);
    }
}
