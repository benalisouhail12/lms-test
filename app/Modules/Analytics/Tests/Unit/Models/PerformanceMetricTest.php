<?php

namespace app\Modules\Analytics\Tests\Unit\Models;

use app\Modules\Analytics\Database\Factories\PerformanceMetricFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceMetricTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        // Arrange
        $data = [
            'name' => 'test_metric',
            'value' => 42.5,
            'previous_value' => 40.0,
            'unit' => 'percent',
            'period' => 'monthly',
            'date_recorded' => now()
        ];

        // Act
        $metric = PerformanceMetricFactory::new()->create($data);

        // Assert
        $this->assertEquals('test_metric', $metric->name);
        $this->assertEquals(42.5, $metric->value);
        $this->assertEquals(40.0, $metric->previous_value);
        $this->assertEquals('percent', $metric->unit);
        $this->assertEquals('monthly', $metric->period);
        $this->assertNotNull($metric->date_recorded);
    }

    public function test_casts_attributes()
    {
        // Arrange & Act
        $metric =  PerformanceMetricFactory::new()->create([
            'value' => '42.5',
            'previous_value' => '40.0',
            'date_recorded' => '2025-03-02 10:00:00'
        ]);

        // Assert - Check if values are cast to proper types
        $this->assertIsFloat($metric->value);
        $this->assertIsFloat($metric->previous_value);
        $this->assertInstanceOf(\Carbon\Carbon::class, $metric->date_recorded);
    }
}
