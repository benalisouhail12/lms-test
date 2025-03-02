<?php
namespace App\Modules\CourseManagement\Tests\Feature;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PerformanceTest extends DuskTestCase
{
    public function testPageLoadSpeed()
    {
        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            $browser->visit('api/course/courses');
            $endTime = microtime(true);

            $this->assertTrue(($endTime - $startTime) < 2); // La page doit se charger en moins de 2 secondes
        });
    }
}
