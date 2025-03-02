<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
use app\Modules\AssignmentSystem\Database\Factories\AssignmentSubmissionFactory;
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

class AssignmentSubmissionTest extends TestCase
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




    public function test_can_get_all_submissions_for_assignment()
    {
        // Create some test submissions
        AssignmentSubmissionFactory::new()->count(3)->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_student_submissions()
    {
        // Create submissions for this student
        AssignmentSubmissionFactory::new()->count(2)->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}/users/{$this->student->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_student_can_submit_assignment()
    {
        Storage::fake('submissions');

        $response = $this->actingAs($this->student)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/submit", [
                'content' => 'This is my submission',
                'files' => [
                    UploadedFile::fake()->create('document.pdf', 1000)
                ]
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'submitted'
        ]);
    }

    public function test_can_show_submission()
    {
        $submission =    AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/submissions/{$submission->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $submission->id,
                'user_id' => $this->student->id
            ]);
    }

    public function test_can_update_submission()
    {
        $submission =    AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->student)
            ->putJson("/api/assingment-system/submissions/{$submission->id}", [
                'content' => 'Updated submission content'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated submission content']);
    }

    public function test_can_submit_draft()
    {
        $submission =    AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson("/api/assingment-system/submissions/{$submission->id}/submit-draft");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'submitted']);
    }




}
