<?php
// app/Modules/Notifications/Events/NotificationCreated.php
namespace App\Modules\Notifications\Events;

use App\Modules\Notifications\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
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
        return 'notification.created';
    }

    public function broadcastWith()
    {
        return [
            'type' => 'notification',
            'payload' => [
                'id' => $this->notification->id,
                'title' => $this->notification->title,
                'message' => $this->notification->message,
                'type' => $this->notification->type,
                'status' => $this->notification->status,
                'priority' => $this->notification->priority,
                'created_at' => $this->notification->created_at->toIso8601String(),
                'group_id' => $this->notification->group_id,
                'meta_data' => $this->notification->meta_data,
            ]
        ];
    }
}






