<?php
namespace app\Modules\Analytics\Tests\Feature\Api;

use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsControllerTest extends TestCase
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
        PerformanceMetricFactory::new()->count(5)->create();

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson('/api/metrics');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'value',
                             'period',
                             'date_recorded'
                         ]
                     ]
                 ]);
    }

    public function test_index_endpoint_with_filters()
    {
        // Arrange
        PerformanceMetricFactory::new()->count(3)->create([
            'name' => 'conversion_rate',
            'period' => 'monthly'
        ]);

        PerformanceMetricFactory::new()->count(2)->create([
            'name' => 'retention_rate',
            'period' => 'monthly'
        ]);

        // Act - Filter by metric type
        $filteredResponse = $this->actingAs($this->user)
                                 ->getJson('/api/metrics?metric_type=conversion_rate');

        // Act - Filter by period and limit
        $limitedResponse = $this->actingAs($this->user)
                                ->getJson('/api/metrics?period=monthly&limit=2');

        // Assert - Filtered response
        $filteredResponse->assertStatus(200);
        $filteredData = $filteredResponse->json('data');
        $this->assertCount(3, $filteredData);

        // Assert - Limited response
        $limitedResponse->assertStatus(200);
        $limitedData = $limitedResponse->json('data');
        $this->assertCount(2, $limitedData);
    }

    public function test_store_endpoint()
    {
        // Arrange
        $metricData = [
            'name' => 'test_metric',
            'value' => 75.5,
            'previous_value' => 70.0,
            'unit' => 'percent',
            'period' => 'monthly',
            'date_recorded' => now()->format('Y-m-d H:i:s')
        ];

        // Act
        $response = $this->actingAs($this->user)
                         ->postJson('/api/metrics', $metricData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'value',
                         'previous_value',
                         'unit',
                         'period',
                         'date_recorded'
                     ]
                 ]);

        $this->assertEquals($metricData['name'], $response->json('data.name'));
        $this->assertEquals($metricData['value'], $response->json('data.value'));
        $this->assertEquals($metricData['unit'], $response->json('data.unit'));
    }

    public function test_show_endpoint()
    {
        // Arrange
        $metric = PerformanceMetricFactory::new()->create([
            'name' => 'test_metric',
            'value' => 85.2
        ]);

        // Act
        $response = $this->actingAs($this->user)
                         ->getJson("/api/metrics/{$metric->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'value',
                         'period',
                         'date_recorded'
                     ]
                 ]);

        $this->assertEquals($metric->id, $response->json('data.id'));
        $this->assertEquals($metric->name, $response->json('data.name'));
        $this->assertEquals($metric->value, $response->json('data.value'));
    }
}
