<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
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

class AssignmentApiTest extends TestCase
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


    public function test_can_get_assignments_for_course()
    {
        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/courses/{$this->course->id}/assignments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'due_date', 'points_possible',
                        'status', 'created_by', 'course_id', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_create_assignment()
    {
        $assignmentData = [
            'course_id' => $this->course->id,
            'title' => 'New Test Assignment',
            'description' => 'This is a test assignment',
            'due_date' => now()->addDays(7)->toDateTimeString(),
            'points_possible' => 100,
            'status' => 'draft'
        ];

        $response = $this->actingAs($this->teacher)
            ->postJson('/api/assingment-system/assignments', $assignmentData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'New Test Assignment',
                'description' => 'This is a test assignment',
            ]);
    }

    public function test_can_show_assignment()
    {
        $response = $this->actingAs($this->student)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->assignment->id,
                'title' => $this->assignment->title
            ]);
    }

    public function test_can_update_assignment()
    {
        $updateData = [
            'title' => 'Updated Assignment Title',
            'description' => 'Updated description',
            'due_date' => now()->addDays(10)->toDateTimeString()
        ];

        $response = $this->actingAs($this->teacher)
            ->putJson("/api/assingment-system/assignments/{$this->assignment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Assignment Title',
                'description' => 'Updated description'
            ]);
    }

    public function test_can_delete_assignment()
    {
        $response = $this->actingAs($this->teacher)
            ->deleteJson("/api/assingment-system/assignments/{$this->assignment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assignments', ['id' => $this->assignment->id]);
    }

    public function test_can_publish_assignment()
    {
        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/publish");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'published']);
    }

    public function test_can_archive_assignment()
    {
        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/archive");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'archived']);
    }






}
