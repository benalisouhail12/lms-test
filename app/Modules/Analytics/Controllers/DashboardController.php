<?php
namespace app\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use app\Modules\Analytics\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function getMetrics()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();
        return response()->json(['data' => $metrics]);
    }

    public function getPerformanceIndicators()
    {
        $indicators = $this->analyticsService->getPerformanceIndicators();
        return response()->json(['data' => $indicators]);
    }

    public function getCourseAnalysis()
    {
        $courses = $this->analyticsService->getCourseAnalytics();
        return response()->json(['data' => $courses]);
    }
}
