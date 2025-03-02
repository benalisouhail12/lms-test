<?php
namespace app\Modules\Analytics\Services;

use app\Modules\Analytics\Models\PerformanceMetric;
use app\Modules\Analytics\Models\Report;
use Carbon\Carbon;

class AnalyticsService
{
    public function getDashboardMetrics()
    {
        // Retrieve the latest metrics for dashboard
        return PerformanceMetric::where('date_recorded', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy('name');
    }

    public function getPerformanceIndicators()
    {
        // Get KPIs
        return PerformanceMetric::whereIn('name', [
            'conversion_rate',
            'retention_rate',
            'engagement_score',
            'satisfaction_index',
            'growth_rate'
        ])->latest('date_recorded')->get()->keyBy('name');
    }

    public function getCourseAnalytics()
    {
        // Simulation des données d'analyse de cours
        return [
            [
                'course_id' => 1,
                'title' => 'Introduction à Laravel',
                'completion_rate' => 87.5,
                'average_score' => 92.3,
                'student_count' => 1250
            ],
            [
                'course_id' => 2,
                'title' => 'Développement avancé avec Laravel',
                'completion_rate' => 76.3,
                'average_score' => 88.1,
                'student_count' => 820
            ]
        ];
    }

    public function getReportsList()
    {
        return Report::latest()->paginate(10);
    }

    public function getReport($id)
    {
        return Report::findOrFail($id);
    }

    public function createCustomReport($title, array $metrics, $period)
    {
        // Create a new custom report
        $report = Report::create([
            'title' => $title,
            'metrics' => $metrics,
            'period' => $period,
            'created_by' => auth()->user()->id
        ]);

        // Generate report data
        $this->generateReportData($report);

        return $report;
    }

    public function updateReport($id, array $data)
    {
        $report = Report::findOrFail($id);
        $report->update($data);

        if (isset($data['metrics']) || isset($data['period'])) {
            $this->generateReportData($report);
        }

        return $report;
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        return $report->delete();
    }

    protected function generateReportData($report)
    {
        // Logic to populate report with actual data
        $metrics = $report->metrics;
        $period = $report->period;

        // Retrieve relevant metrics and store with report
        $data = PerformanceMetric::whereIn('name', $metrics)
            ->where('period', $period)
            ->get();

        $report->data = $data;
        $report->save();
    }

    public function getMetricsData($type = 'all', $period = 'monthly', $limit = 10)
    {
        $query = PerformanceMetric::where('period', $period);

        if ($type !== 'all') {
            $query->where('name', $type);
        }

        return $query->latest('date_recorded')->limit($limit)->get();
    }

    public function getMetric($id)
    {
        return PerformanceMetric::findOrFail($id);
    }

    public function createMetric(array $data)
    {
        return PerformanceMetric::create($data);
    }
}
