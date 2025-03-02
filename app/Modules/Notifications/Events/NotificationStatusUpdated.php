<?php
namespace App\Modules\Notifications\Events;

use App\Modules\Notifications\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.user.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'notification.status_updated';
    }

    public function broadcastWith()
    {
        return [
            'type' => 'status_update',
            'payload' => [
                'id' => $this->notification->id,
                'status' => $this->notification->status,
                'updated_at' => $this->notification->updated_at->toIso8601String(),
            ]
        ];
    }
}
