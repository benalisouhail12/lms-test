<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
use app\Modules\AssignmentSystem\Database\Factories\GradingCriteriaFactory;
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

class GradingCriteriaControllerTest extends TestCase
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


    public function test_can_get_assignment_criteria()
    {
        GradingCriteriaFactory::new()->count(3)->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}/criteria");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_grading_criteria()
    {
        $criteriaData = [
            'title' => 'Code Quality',
            'description' => 'Clean, readable, and well-documented code',
            'points' => 25
        ];

        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/criteria", $criteriaData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Code Quality',
                'points' => 25
            ]);
    }

    public function test_can_update_criteria()
    {
        $criteria =  GradingCriteriaFactory::new()->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->putJson("/api/assingment-system/criteria/{$criteria->id}", [
                'title' => 'Updated Criteria',
                'points' => 30
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Criteria',
                'points' => 30
            ]);
    }

    public function test_can_delete_criteria()
    {
        $criteria =  GradingCriteriaFactory::new()->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->deleteJson("/api/assingment-system/criteria/{$criteria->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('grading_criteria', ['id' => $criteria->id]);
    }


}
