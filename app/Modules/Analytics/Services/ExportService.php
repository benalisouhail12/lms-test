<?php
namespace app\Modules\Analytics\Services;

use app\Modules\Analytics\Models\PerformanceMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ExportService
{
    public function exportData($format, $dataType, $period)
    {
        // Get data based on type and period
        $data = $this->getData($dataType, $period);

        // Export based on requested format
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data, $dataType);
            case 'pdf':
                return $this->exportToPdf($data, $dataType);
            case 'excel':
                return $this->exportToExcel($data, $dataType);
            case 'json':
                return $this->exportToJson($data);
            default:
                throw new \Exception("Format non supporté");
        }
    }

    private function getData($dataType, $period)
    {
        $startDate = $this->getPeriodStartDate($period);

        $query = PerformanceMetric::where('date_recorded', '>=', $startDate);

        if ($dataType !== 'all') {
            $query->where('name', $dataType);
        }

        return $query->get();
    }

    private function getPeriodStartDate($period)
    {
        switch ($period) {
            case 'daily':
                return Carbon::now()->subDay();
            case 'weekly':
                return Carbon::now()->subWeek();
            case 'monthly':
                return Carbon::now()->subMonth();
            case 'quarterly':
                return Carbon::now()->subMonths(3);
            case 'yearly':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subMonth();
        }
    }

    private function exportToCsv($data, $fileName)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}.csv\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Value', 'Previous Value', 'Unit', 'Period', 'Date']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->name,
                    $row->value,
                    $row->previous_value,
                    $row->unit,
                    $row->period,
                    $row->date_recorded
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportToJson($data)
    {
        return response()->json(['data' => $data]);
    }

    // Méthodes d'export PDF et Excel disponibles si vous ajoutez les packages nécessaires
    private function exportToPdf($data, $fileName)
    {
        // Implémentation avec package PDF comme dompdf
        throw new \Exception("Installation du package PDF requise");
    }

    private function exportToExcel($data, $fileName)
    {
        // Implémentation avec package Excel comme Laravel Excel
        throw new \Exception("Installation du package Excel requise");
    }
}
