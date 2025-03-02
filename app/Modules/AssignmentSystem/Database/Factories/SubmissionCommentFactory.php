<?php

namespace app\Modules\AssignmentSystem\Database\Factories;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentSubmissionFactory;
use App\Modules\AssignmentSystem\Models\SubmissionComment;

use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionCommentFactory extends Factory
{
    protected $model = SubmissionComment::class;

    public function definition()
    {
        return [
            'assignment_submission_id' => AssignmentSubmissionFactory::new()->create()->id,
            'user_id' => UserFactory::new()->create()->id,
            'comment' => $this->faker->sentence(),
            'attachment' => [$this->faker->url()],
            'is_private' => $this->faker->boolean(),
            'parent_comment_id' => null, // Optional for parent comments
            'comment_location' => [$this->faker->latitude(), $this->faker->longitude()],
        ];
    }

    public function private()
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    public function withParent(SubmissionComment $parent)
    {
        return $this->state(fn (array $attributes) => [
            'parent_comment_id' => $parent->id,
        ]);
    }
}
