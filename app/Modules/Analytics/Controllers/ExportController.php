<?php
namespace app\Modules\Analytics\Controllers;
use App\Http\Controllers\Controller;
use app\Modules\Analytics\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf,excel,json',
            'data_type' => 'required|string',
            'period' => 'required|string'
        ]);

        return $this->exportService->exportData(
            $request->format,
            $request->data_type,
            $request->period
        );
    }
}
