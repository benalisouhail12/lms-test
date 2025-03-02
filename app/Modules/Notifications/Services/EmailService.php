<?php
namespace App\Modules\Notifications\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\Notifications\Models\Notification;
use App\Modules\Notifications\Models\NotificationPreference;
use Illuminate\Support\Facades\Mail;
use App\Modules\Notifications\Mail\NotificationDigest;
use App\Modules\Notifications\Models\NotificationHistory;

class EmailService
{
    public function sendDigest($frequency)
    {
        // Get all users who have email digest set to this frequency
        $users = NotificationPreference::whereJsonContains('channels->email->frequency', $frequency)
            ->get()
            ->pluck('user_id');

        foreach ($users as $userId) {
            $this->sendUserDigest($userId, $frequency);
        }
    }

    protected function sendUserDigest($userId, $frequency)
    {
        // Get user
        $user =User::find($userId);

        if (!$user || !$user->email) {
            return;
        }

        // Get preferences
        $preferences = NotificationPreference::where('user_id', $userId)->first();

        if (!$preferences) {
            return;
        }

        // Determine date range based on frequency
        $startDate = $this->getDigestStartDate($frequency);

        // Get unread notifications from this period
        $notifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Skip if no notifications
        if ($notifications->isEmpty()) {
            return;
        }

        // Send digest email
        Mail::to($user)->send(new NotificationDigest($user, $notifications, $frequency));

        // Update notification history
        foreach ($notifications as $notification) {
            // Record in history that this was sent via digest
            NotificationHistory::create([
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'channel' => 'email',
                'status' => 'sent',
                'details' => ['method' => 'digest', 'frequency' => $frequency]
            ]);
        }
    }

    protected function getDigestStartDate($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return now()->subDay();
            case 'weekly':
                return now()->subWeek();
            case 'monthly':
                return now()->subMonth();
            default:
                return now()->subDay();
        }
    }
}
