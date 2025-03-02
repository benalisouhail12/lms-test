<?php
namespace App\Modules\Notifications\Controllers;

use App\Modules\Notifications\Models\NotificationHistory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $notificationId = $request->input('notification_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);

        $query = NotificationHistory::where('user_id', $userId);

        if ($notificationId) {
            $query->where('notification_id', $notificationId);
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $history = $query->orderBy('created_at', 'desc')
                        ->paginate($limit, ['*'], 'page', $page);

        return response()->json($history);
    }
}
