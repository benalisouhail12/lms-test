<?php

namespace App\Modules\AssignmentSystem\Tests\Feature;

use app\Modules\AssignmentSystem\Database\Factories\AssignmentFactory;
use app\Modules\AssignmentSystem\Database\Factories\AssignmentGroupFactory;
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

class AssignmentGroupTest extends TestCase
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




    public function test_can_get_assignment_groups()
    {
        AssignmentGroupFactory::new()->count(3)->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->getJson("/api/assingment-system/assignments/{$this->assignment->id}/groups");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_group()
    {
        $groupData = [
            'name' => 'Project Team Alpha',
            'member_ids' => [$this->student->id, User::factory()->create()->id]
        ];

        $response = $this->actingAs($this->teacher)
            ->postJson("/api/assingment-system/assignments/{$this->assignment->id}/groups", $groupData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Project Team Alpha']);
    }

    public function test_can_update_group()
    {
        $group =  AssignmentGroupFactory::new()->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->putJson("/api/assingment-system/groups/{$group->id}", [
                'name' => 'Updated Group Name'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Group Name']);
    }

    public function test_can_delete_group()
    {
        $group =  AssignmentGroupFactory::new()->create([
            'assignment_id' => $this->assignment->id
        ]);

        $response = $this->actingAs($this->teacher)
            ->deleteJson("/api/assingment-system/groups/{$group->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assignment_groups', ['id' => $group->id]);
    }


}
