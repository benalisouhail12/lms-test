<?php
namespace app\Modules\Analytics\Tests\Unit\Services;

use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use app\Modules\Analytics\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $exportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportService = new ExportService();
    }

    public function test_get_data_for_export()
    {
        // Arrange
        PerformanceMetricFactory::new()->count(5)->create([
            'name' => 'conversion_rate',
            'date_recorded' => Carbon::now()->subDays(10)
        ]);
        PerformanceMetricFactory::new()->count(3)->create([
            'name' => 'retention_rate',
            'date_recorded' => Carbon::now()->subDays(20)
        ]);

        // Use reflection to make private method accessible
        $method = new \ReflectionMethod(ExportService::class, 'getData');
        $method->setAccessible(true);

        // Act
        $specificData = $method->invoke($this->exportService, 'conversion_rate', 'monthly');
        $allData = $method->invoke($this->exportService, 'all', 'monthly');

        // Assert
        $this->assertCount(5, $specificData);
        $this->assertCount(8, $allData);
    }

    public function test_get_period_start_date()
    {
        // Use reflection to make private method accessible
        $method = new \ReflectionMethod(ExportService::class, 'getPeriodStartDate');
        $method->setAccessible(true);

        // Act
        $dailyDate = $method->invoke($this->exportService, 'daily');
        $weeklyDate = $method->invoke($this->exportService, 'weekly');
        $monthlyDate = $method->invoke($this->exportService, 'monthly');
        $quarterlyDate = $method->invoke($this->exportService, 'quarterly');
        $yearlyDate = $method->invoke($this->exportService, 'yearly');
        $defaultDate = $method->invoke($this->exportService, 'unknown');

        // Assert
        $this->assertEquals(Carbon::now()->subDay()->startOfDay()->format('Y-m-d'),
                           $dailyDate->startOfDay()->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->subWeek()->startOfDay()->format('Y-m-d'),
                           $weeklyDate->startOfDay()->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->subMonth()->startOfDay()->format('Y-m-d'),
                           $monthlyDate->startOfDay()->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->subMonths(3)->startOfDay()->format('Y-m-d'),
                           $quarterlyDate->startOfDay()->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->subYear()->startOfDay()->format('Y-m-d'),
                           $yearlyDate->startOfDay()->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->subMonth()->startOfDay()->format('Y-m-d'),
                           $defaultDate->startOfDay()->format('Y-m-d'));
    }

    public function test_export_to_json()
    {
        // Arrange
        $data =  PerformanceMetricFactory::new()->count(3)->create();

        // Use reflection to make private method accessible
        $method = new \ReflectionMethod(ExportService::class, 'exportToJson');
        $method->setAccessible(true);

        // Act
        $response = $method->invoke($this->exportService, $data);

        // Assert
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertCount(3, $responseData['data']);
    }
}
