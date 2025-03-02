<?php
namespace app\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use app\Modules\Analytics\Services\AnalyticsService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $reports = $this->analyticsService->getReportsList();
        return response()->json(['data' => $reports]);
    }

    public function show($id)
    {
        $report = $this->analyticsService->getReport($id);
        return response()->json(['data' => $report]);
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'metrics' => 'required|array',
            'period' => 'required|string'
        ]);

        $report = $this->analyticsService->createCustomReport(
            $request->title,
            $request->metrics,
            $request->period
        );

        return response()->json([
            'message' => 'Rapport créé avec succès',
            'data' => $report
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'metrics' => 'sometimes|array',
            'period' => 'sometimes|string'
        ]);

        $report = $this->analyticsService->updateReport($id, $request->all());

        return response()->json([
            'message' => 'Rapport mis à jour avec succès',
            'data' => $report
        ]);
    }

    public function destroy($id)
    {
        $this->analyticsService->deleteReport($id);

        return response()->json([
            'message' => 'Rapport supprimé avec succès'
        ]);
    }
}
