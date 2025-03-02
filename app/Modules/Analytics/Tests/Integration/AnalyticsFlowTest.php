<?php
namespace app\Modules\Analytics\Tests\Integration;

use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    public function test_complete_analytics_flow()
    {
        // Step 1: Create some metrics
        $metricData = [
            'name' => 'conversion_rate',
            'value' => 12.5,
            'previous_value' => 10.2,
            'unit' => 'percent',
            'period' => 'monthly',
            'date_recorded' => now()->format('Y-m-d H:i:s')
        ];

        $metricResponse = $this->actingAs($this->user)
                               ->postJson('/api/metrics', $metricData);

        $metricResponse->assertStatus(201);
        $metricId = $metricResponse->json('data.id');

        // Step 2: Verify created metric
        $this->actingAs($this->user)
             ->getJson("/api/metrics/{$metricId}")
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => $metricId,
                     'name' => 'conversion_rate',
                     'value' => 12.5
                 ]
             ]);

        // Step 3: Create a report using the metric
        $reportData = [
            'title' => 'Conversion Analysis',
            'metrics' => ['conversion_rate'],
            'period' => 'monthly'
        ];

        $reportResponse = $this->actingAs($this->user)
                               ->postJson('/api/reports', $reportData);

        $reportResponse->assertStatus(201);
        $reportId = $reportResponse->json('data.id');

        // Step 4: Verify the report
        $this->actingAs($this->user)
             ->getJson("/api/reports/{$reportId}")
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => $reportId,
                     'title' => 'Conversion Analysis'
                 ]
             ]);

        // Step 5: Check dashboard data
        $this->actingAs($this->user)
             ->getJson('/api/dashboard/metrics')
             ->assertStatus(200)
             ->assertJsonStructure(['data']);

        // Step 6: Export the data
        $exportData = [
            'format' => 'json',
            'data_type' => 'conversion_rate',
            'period' => 'monthly'
        ];

        $this->actingAs($this->user)
             ->postJson('/api/export', $exportData)
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }
}
