<?php

namespace app\Modules\StudentPortal\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use app\Modules\CourseManagement\Database\Factories\CourseFactory;
use app\Modules\CourseManagement\Database\Factories\LessonFactory;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use App\Modules\CourseManagement\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class ProgressControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $course;
    protected $lesson;
    protected $learningPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
        $this->course =     CourseFactory::new()->create();
        $this->lesson = LessonFactory::new()->create(['course_id' => $this->course->id]);
        $this->learningPath = LearningPath::factory()->create();
    }

    /** @test */
    public function it_can_get_progress_overview()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/progress/overview');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_course_progress()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/student/progress/courses/{$this->course->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'course_id',
                    'progress_percentage',
                    // Add other expected progress fields
                ]
            ]);
    }

    /** @test */
    public function it_can_get_learning_path_progress()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/student/progress/learning-paths/{$this->learningPath->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_mark_lesson_as_complete()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/student/progress/lessons/{$this->lesson->id}/complete");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_track_time_spent_on_lesson()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/student/progress/lessons/{$this->lesson->id}/track-time", [
                'seconds_spent' => 300,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_validates_time_spent_tracking()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/student/progress/lessons/{$this->lesson->id}/track-time", [
                'seconds_spent' => 'not-a-number',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('seconds_spent');
    }
}
