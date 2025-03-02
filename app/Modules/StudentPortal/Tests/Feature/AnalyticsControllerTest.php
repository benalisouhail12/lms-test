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
class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    /** @test */
    public function it_can_get_overall_performance()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/analytics/performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_activities_performance()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/analytics/activities-performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_assessments_results()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/analytics/assessments-results');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_completion_rate()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/analytics/completion-rate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /** @test */
    public function it_can_get_engagement_metrics()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/analytics/engagement-metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }
}
