<?php
namespace app\Modules\Analytics\Tests\Feature\Api;

use App\Models\PerformanceMetric;
use App\Models\User;
use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();

        // Seed some metrics for export
        PerformanceMetricFactory::new()->count(5)->create([
            'period' => 'monthly'
        ]);
    }

    public function test_export_json_endpoint()
    {
        // Arrange
        $exportData = [
            'format' => 'json',
            'data_type' => 'all',
            'period' => 'monthly'
        ];

        // Act
        $response = $this->actingAs($this->user)
                         ->postJson('/api/export', $exportData);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function test_export_csv_endpoint()
    {
        // Arrange
        $exportData = [
            'format' => 'csv',
            'data_type' => 'all',
            'period' => 'monthly'
        ];

        // Act
        $response = $this->actingAs($this->user)
                         ->postJson('/api/export', $exportData);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition'));
    }

    public function test_export_validation()
    {
        // Arrange - Missing format
        $invalidData1 = [
            'data_type' => 'all',
            'period' => 'monthly'
        ];

        // Arrange - Invalid format
        $invalidData2 = [
            'format' => 'invalid_format',
            'data_type' => 'all',
            'period' => 'monthly'
        ];

        // Act & Assert - Missing format
        $this->actingAs($this->user)
             ->postJson('/api/export', $invalidData1)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['format']);

        // Act & Assert - Invalid format
        $this->actingAs($this->user)
             ->postJson('/api/export', $invalidData2)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['format']);
    }
}
