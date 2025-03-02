<?php
namespace app\Modules\Analytics\Tests\Feature\Api;

use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    public function test_get_metrics_endpoint()
    {
        // Arrange
        PerformanceMetricFactory::new()->count(3)->create();

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson('/api/dashboard/metrics');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_get_performance_indicators_endpoint()
    {
        // Arrange
        PerformanceMetricFactory::new()->create([
            'name' => 'conversion_rate',
            'value' => 12.5
        ]);

        PerformanceMetricFactory::new()->create([
            'name' => 'retention_rate',
            'value' => 85.2
        ]);

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson('/api/dashboard/indicators');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_get_course_analysis_endpoint()
    {
        // Act
        $response = $this->actingAs($this->user)
                         ->getJson('/api/dashboard/courses');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'course_id',
                             'title',
                             'completion_rate',
                             'average_score',
                             'student_count'
                         ]
                     ]
                 ]);
    }
}
