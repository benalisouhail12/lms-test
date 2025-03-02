<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
use app\Modules\AssignmentSystem\Database\Factories\AssignmentSubmissionFactory;
use app\Modules\AssignmentSystem\Database\Factories\SubmissionCommentFactory;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\AssignmentExtension;
use App\Modules\AssignmentSystem\Models\AssignmentGroup;
use App\Modules\AssignmentSystem\Models\AssignmentSubmission;
use App\Modules\AssignmentSystem\Models\Grade;
use App\Modules\AssignmentSystem\Models\GradingCriteria;
use App\Modules\AssignmentSystem\Models\SubmissionComment;
use app\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use app\Modules\CourseManagement\Database\Factories\CourseFactory;
use App\Modules\CourseManagement\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentCommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $teacher;
    protected $student;
    protected $course;
    protected $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with appropriate roles
        $this->teacher = UserFactory::new()->create();
        $this->teacher->assignRole('teacher');
        $this->student = UserFactory::new()->create();
        $this->student->assignRole('student');


        // Create a test course
        $this->course = CourseFactory::new()->create();

        // Create a test assignment
        $this->assignment = AssignmentFactory::new()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->teacher->id
        ]);
    }



    public function test_can_get_submission_comments()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        SubmissionCommentFactory::new()->count(3)->create([
            'submission_id' => $submission->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/submissions/{$submission->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_comment()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/submissions/{$submission->id}/comments", [
                'content' => 'Great job on this section!',
                'is_private' => false
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'content' => 'Great job on this section!',
                'user_id' => $this->teacher->id
            ]);
    }

    public function test_can_update_comment()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $comment = SubmissionCommentFactory::new()->create([
            'submission_id' => $submission->id,
            'user_id' => $this->teacher->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->putJson("/api/assingment-system/comments/{$comment->id}", [
                'content' => 'Updated comment content'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated comment content']);
    }

    public function test_can_delete_comment()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $comment = SubmissionCommentFactory::new()->create([
            'submission_id' => $submission->id,
            'user_id' => $this->teacher->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->deleteJson("/api/assingment-system/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('submission_comments', ['id' => $comment->id]);
    }



}
