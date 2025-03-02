<?php
namespace  app\Modules\Analytics\Tests\Feature\Api;

use App\Models\Report;
use App\Models\User;
use app\Modules\Analytics\Database\Factories\ReportFactory;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    public function test_index_endpoint()
    {
        // Arrange
        ReportFactory::new()->count(3)->create([
            'created_by' => $this->user->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson('/api/reports');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'metrics',
                             'period'
                         ]
                     ]
                 ]);
    }

    public function test_store_endpoint()
    {
        // Arrange
        $reportData = [
            'title' => 'New Test Report',
            'metrics' => ['conversion_rate', 'retention_rate'],
            'period' => 'monthly'
        ];

        // Act
        $response = $this->actingAs($this->user)
                         ->postJson('/api/reports', $reportData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'metrics',
                         'period',
                         'created_by'
                     ]
                 ]);

        $this->assertEquals($reportData['title'], $response->json('data.title'));
        $this->assertEquals($reportData['metrics'], $response->json('data.metrics'));
        $this->assertEquals($reportData['period'], $response->json('data.period'));
        $this->assertEquals($this->user->id, $response->json('data.created_by'));
    }

    public function test_show_endpoint()
    {
        // Arrange
        $report = ReportFactory::new()->create([
            'created_by' => $this->user->id,
            'title' => 'Test Report',
            'metrics' => ['conversion_rate', 'retention_rate'],
            'period' => 'monthly'
        ]);

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson("/api/reports/{$report->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'metrics',
                         'period',
                         'created_by'
                     ]
                 ]);

        $this->assertEquals($report->id, $response->json('data.id'));
        $this->assertEquals($report->title, $response->json('data.title'));
    }

    public function test_update_endpoint()
    {
        // Arrange
        $report = ReportFactory::new()->create([
            'created_by' => $this->user->id,
            'title' => 'Original Title',
            'metrics' => ['conversion_rate'],
            'period' => 'monthly'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'metrics' => ['conversion_rate', 'retention_rate'],
            'period' => 'quarterly'
        ];

        // Act
        $response = $this->actingAs($this->user)
                         ->putJson("/api/reports/{$report->id}", $updateData);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'metrics',
                         'period',
                         'created_by'
                     ]
                 ]);

        $this->assertEquals($updateData['title'], $response->json('data.title'));
        $this->assertEquals($updateData['metrics'], $response->json('data.metrics'));
        $this->assertEquals($updateData['period'], $response->json('data.period'));
    }

    public function test_destroy_endpoint()
    {
        // Arrange
        $report = ReportFactory::new()->create([
            'created_by' => $this->user->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
                         ->deleteJson("/api/reports/{$report->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }
}
