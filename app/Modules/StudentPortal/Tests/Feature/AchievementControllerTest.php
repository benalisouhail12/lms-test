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
class AchievementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
        $this->course = Course::factory()->create();

        // Set up any relationships needed for certificate testing
        // For example, enroll the user in the course and mark it as completed
    }

    /** @test */
    public function it_can_get_all_achievements()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/achievements');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_user_badges()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/achievements/badges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_user_certificates()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/achievements/certificates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_download_certificate()
    {
        // Mock the necessary conditions for certificate download
        // This might require additional setup depending on your implementation

        $response = $this->actingAs($this->user)
            ->getJson("/api/student/achievements/certificates/{$this->course->id}/download");

        // Depending on your implementation, this might return a file download
        // or a JSON response with a download link
        $response->assertStatus(200);
    }
}
