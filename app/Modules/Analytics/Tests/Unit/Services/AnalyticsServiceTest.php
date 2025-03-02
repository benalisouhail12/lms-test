<?php
namespace  app\Modules\Analytics\Tests\Unit\Services;

use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use app\Modules\Analytics\Services\AnalyticsService;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyticsService = new AnalyticsService();
    }

    public function test_get_dashboard_metrics_returns_grouped_data()
    {
        // Arrange
        PerformanceMetricFactory::new()->create([
            'name' => 'conversion_rate',
            'value' => 15.3,
            'date_recorded' => Carbon::now()->subDays(10)
        ]);

        PerformanceMetricFactory::new()->create([
            'name' => 'conversion_rate',
            'value' => 16.1,
            'date_recorded' => Carbon::now()->subDays(5)
        ]);

        PerformanceMetricFactory::new()->create([
            'name' => 'retention_rate',
            'value' => 85.7,
            'date_recorded' => Carbon::now()->subDays(8)
        ]);

        // Act
        $result = $this->analyticsService->getDashboardMetrics();

        // Assert
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('conversion_rate', $result);
        $this->assertArrayHasKey('retention_rate', $result);
        $this->assertCount(2, $result['conversion_rate']);
        $this->assertCount(1, $result['retention_rate']);
    }

    public function test_create_custom_report()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $this->actingAs($user);

        $title = 'Test Report';
        $metrics = ['conversion_rate', 'retention_rate'];
        $period = 'monthly';

        // Create some metrics for the report to use
        PerformanceMetricFactory::new()->create([
            'name' => 'conversion_rate',
            'period' => 'monthly'
        ]);

        // Act
        $report = $this->analyticsService->createCustomReport($title, $metrics, $period);

        // Assert
        $this->assertEquals($title, $report->title);
        $this->assertEquals($metrics, $report->metrics);
        $this->assertEquals($period, $report->period);
        $this->assertEquals($user->id, $report->created_by);
        $this->assertNotNull($report->data);
    }

    public function test_get_metrics_data_returns_filtered_data()
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

        PerformanceMetricFactory::new()->count(1)->create([
            'name' => 'conversion_rate',
            'period' => 'weekly'
        ]);

        // Act - Get only conversion rate metrics
        $filteredResult = $this->analyticsService->getMetricsData('conversion_rate', 'monthly', 10);

        // Act - Get all metrics for monthly period
        $allMonthlyResult = $this->analyticsService->getMetricsData('all', 'monthly', 10);

        // Act - Get limited results
        $limitedResult = $this->analyticsService->getMetricsData('all', 'monthly', 2);

        // Assert
        $this->assertCount(3, $filteredResult);
        $this->assertCount(5, $allMonthlyResult);
        $this->assertCount(2, $limitedResult);
    }
}
