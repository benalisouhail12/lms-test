<?php


namespace App\Modules\StudentPortal\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\StudentPortal\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function performance(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getOverallPerformance($request->user())
        ]);
    }

    public function activitiesPerformance(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getActivitiesPerformance($request->user())
        ]);
    }

    public function assessmentsResults(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getAssessmentsResults($request->user())
        ]);
    }

    public function completionRate(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getCompletionRate($request->user())
        ]);
    }

    public function engagementMetrics(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getEngagementMetrics($request->user())
        ]);
    }
}



