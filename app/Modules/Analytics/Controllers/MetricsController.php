<?php
namespace app\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use app\Modules\Analytics\Services\AnalyticsService;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $metrics = $this->analyticsService->getMetricsData(
            $request->metric_type ?? 'all',
            $request->period ?? 'monthly',
            $request->limit ?? 10
        );

        return response()->json(['data' => $metrics]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'value' => 'required|numeric',
            'previous_value' => 'nullable|numeric',
            'unit' => 'nullable|string',
            'period' => 'required|string',
            'date_recorded' => 'required|date'
        ]);

        $metric = $this->analyticsService->createMetric($request->all());

        return response()->json([
            'message' => 'Métrique créée avec succès',
            'data' => $metric
        ], 201);
    }

    public function show($id)
    {
        $metric = $this->analyticsService->getMetric($id);
        return response()->json(['data' => $metric]);
    }
}
