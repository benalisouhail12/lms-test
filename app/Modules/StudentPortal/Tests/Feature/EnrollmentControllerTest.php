<?php

namespace app\Modules\StudentPortal\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use App\Modules\CourseManagement\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class EnrollmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $course;
    protected $learningPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
        $this->course = Course::factory()->create();
        $this->learningPath = LearningPath::factory()->create();
    }

    /** @test */
    public function it_can_get_available_courses()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/enrollment/available-courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        // Add other expected course fields
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_get_enrolled_courses()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/enrollment/enrolled-courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_enroll_in_course()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/student/enrollment/courses/{$this->course->id}/enroll");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_drop_course()
    {
        // First enroll the user
        $this->actingAs($this->user)
            ->postJson("/api/student/enrollment/courses/{$this->course->id}/enroll");

        // Then drop the course
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/student/enrollment/courses/{$this->course->id}/drop");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_get_available_learning_paths()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/enrollment/available-learning-paths');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        // Add other expected learning path fields
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_enroll_in_learning_path()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/student/enrollment/learning-paths/{$this->learningPath->id}/enroll");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
