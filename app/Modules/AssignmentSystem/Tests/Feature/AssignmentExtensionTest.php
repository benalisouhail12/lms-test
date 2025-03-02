<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentExtensionFactory;
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

class AssignmentExtensionTest extends TestCase
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



    public function test_can_grant_extension()
    {
        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/extensions", [
                'user_id' => $this->student->id,
                'extended_date' => now()->addDays(3)->toDateTimeString(),
                'reason' => 'Medical emergency'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('assignment_extensions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);
    }

    public function test_can_revoke_extension()
    {
        $extension = AssignmentExtensionFactory ::new()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->deleteJson("/api/assingment-system/assignments/{$this->assignment->id}/extensions/{$this->student->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assignment_extensions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id
        ]);
    }

    public function test_can_list_extensions()
    {
        AssignmentExtensionFactory ::new()->count(3)->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}/extensions");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
