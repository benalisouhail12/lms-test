<?php
// app/Modules/Notifications/Controllers/NotificationController.php
namespace App\Modules\Notifications\Controllers;

use App\Modules\Notifications\Models\Notification;
use App\Modules\Notifications\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $status = $request->input('status');
        $type = $request->input('type');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 15);

        $query = Notification::forUser($userId)->active();

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($limit, ['*'], 'page', $page);

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'meta_data' => 'nullable|array',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $notification = $this->notificationService->create($validatedData);

        return response()->json($notification, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:read,unread,dismissed',
        ]);

        $userId = $request->user()->id;
        $notification = Notification::where('id', $id)
                                   ->where('user_id', $userId)
                                   ->firstOrFail();

        $notification->status = $validatedData['status'];
        $notification->save();

        $this->notificationService->broadcastStatusUpdate($notification);

        return response()->json($notification);
    }

    public function destroy($id)
    {
        $userId = request()->user()->id;
        $notification = Notification::where('id', $id)
                                   ->where('user_id', $userId)
                                   ->firstOrFail();

        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id;
        $type = $request->input('type');

        $query = Notification::forUser($userId)->unread();

        if ($type) {
            $query->where('type', $type);
        }

        $count = $query->update(['status' => 'read']);

        $this->notificationService->broadcastBulkUpdate($userId, $type);

        return response()->json([
            'message' => "{$count} notifications marked as read"
        ]);
    }
}







