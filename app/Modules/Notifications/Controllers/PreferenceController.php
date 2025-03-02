<?php
namespace App\Modules\Notifications\Controllers;

use App\Modules\Notifications\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(Request $request)
    {
        $userId = $request->user()->id;

        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            [
                'channels' => [
                    'email' => ['enabled' => true, 'frequency' => 'immediate'],
                    'webSocket' => ['enabled' => true],
                    'push' => ['enabled' => true]
                ],
                'notification_types' => [
                    'system' => ['enabled' => true, 'channels' => ['email', 'webSocket', 'push']],
                    'message' => ['enabled' => true, 'channels' => ['email', 'webSocket', 'push']],
                    'update' => ['enabled' => true, 'channels' => ['email', 'webSocket']]
                ],
                'quiet_hours' => [
                    'enabled' => false,
                    'start' => '22:00',
                    'end' => '07:00',
                    'timezone' => 'UTC'
                ],
                'digest_settings' => [
                    'frequency' => 'daily',
                    'dayOfWeek' => 1,
                    'timeOfDay' => '09:00'
                ]
            ]
        );

        return response()->json($preferences);
    }

    public function update(Request $request)
    {
        $userId = $request->user()->id;

        $validatedData = $request->validate([
            'channels' => 'sometimes|required|array',
            'notification_types' => 'sometimes|required|array',
            'quiet_hours' => 'sometimes|nullable|array',
            'digest_settings' => 'sometimes|nullable|array',
        ]);

        $preferences = NotificationPreference::updateOrCreate(
            ['user_id' => $userId],
            $validatedData
        );

        return response()->json($preferences);
    }
}
