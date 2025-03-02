<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
use app\Modules\AssignmentSystem\Database\Factories\AssignmentSubmissionFactory;
use app\Modules\AssignmentSystem\Database\Factories\GradeFactory;
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

class GradeControllerTest extends TestCase
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




    public function test_teacher_can_grade_submission()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/submissions/{$submission->id}/grade", [
                'score' => 85,
                'feedback' => 'Good work, but could improve in some areas.'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('grades', [
            'submission_id' => $submission->id,
            'score' => 85,
            'graded_by' => $this->teacher->id
        ]);
    }

    public function test_can_show_grade()
    {
        $submission = AssignmentSubmissionFactory::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'submitted'
        ]);

        $grade = GradeFactory::new()->create([
            'submission_id' => $submission->id,
            'score' => 90,
            'graded_by' => $this->teacher->id
        ]);

        $response = $this->actingAs($this->student)
            ->getJson("/api/assingment-system/submissions/{$submission->id}/grade");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'score' => 90,
                'submission_id' => $submission->id
            ]);
    }


}
