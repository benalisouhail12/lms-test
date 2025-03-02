<?php
namespace App\Modules\Notifications\Services;

use App\Modules\Notifications\Models\Notification;
use App\Modules\Notifications\Models\NotificationHistory;
use App\Modules\Notifications\Models\NotificationPreference;
use App\Modules\Notifications\Events\NotificationCreated;
use App\Modules\Notifications\Events\NotificationStatusUpdated;
use App\Modules\Notifications\Events\NotificationsBulkUpdated;
use App\Modules\Notifications\Mail\SingleNotification;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class NotificationService
{
    protected $emailService;
    protected $groupingService;

    public function __construct(
        EmailService $emailService,
        GroupingService $groupingService
    ) {
        $this->emailService = $emailService;
        $this->groupingService = $groupingService;
    }

    public function create(array $data)
    {
        // Generate UUID for the notification
        $data['id'] = Uuid::uuid4()->toString();

        // Check if this notification should be grouped
        $data['group_id'] = $this->groupingService->determineGroupId($data);

        // Create notification
        $notification = Notification::create($data);

        // Get user preferences
        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $data['user_id']]
        );

        // Check if user has enabled this notification type
        if (!$preferences->isTypeEnabled($data['type'])) {
            // Still create the notification but don't send it
            return $notification;
        }

        // Check for quiet hours
        $isInQuietHours = $preferences->isInQuietHours();

        // Determine which channels to use for this notification
        $enabledChannels = $preferences->getEnabledChannelsForType($data['type']);

        // Send via WebSocket (real-time)
        if (in_array('webSocket', $enabledChannels) && !$isInQuietHours) {
            $this->sendViaWebSocket($notification);
        }

        // Send via email
        if (in_array('email', $enabledChannels)) {
            $emailFrequency = $preferences->channels['email']['frequency'] ?? 'immediate';

            if ($emailFrequency === 'immediate' && !$isInQuietHours) {
                $this->sendViaEmail($notification);
            }
            // For digest, it will be handled by a scheduled job
        }

        // Send via push notification
        if (in_array('push', $enabledChannels) && !$isInQuietHours) {
            $this->sendViaPush($notification);
        }

        return $notification;
    }

    protected function sendViaWebSocket(Notification $notification)
    {
        // Broadcast the notification via Laravel Events
        event(new NotificationCreated($notification));

        // Record in history
        $this->recordHistory($notification, 'webSocket', 'sent');
    }

    protected function sendViaEmail(Notification $notification)
    {
        // Get user email
        $user = $notification->user;

        if (!$user || !$user->email) {
            $this->recordHistory($notification, 'email', 'failed', ['reason' => 'No valid email address']);
            return;
        }

        try {
            // Send email
            Mail::to($user)->send(new SingleNotification($notification));

            // Record in history
            $this->recordHistory($notification, 'email', 'sent');
        } catch (\Exception $e) {
            // Record failure
            $this->recordHistory($notification, 'email', 'failed', ['error' => $e->getMessage()]);
        }
    }

    protected function sendViaPush(Notification $notification)
    {


        // Record that we attempted to send
        $this->recordHistory($notification, 'push', 'pending');

    }

    protected function recordHistory(Notification $notification, $channel, $status, array $details = [])
    {
        NotificationHistory::create([
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'channel' => $channel,
            'status' => $status,
            'details' => $details
        ]);
    }

    public function broadcastStatusUpdate(Notification $notification)
    {
        event(new NotificationStatusUpdated($notification));
    }

    public function broadcastBulkUpdate($userId, $type = null)
    {
        event(new NotificationsBulkUpdated($userId, $type));
    }
}
