<?php
namespace App\Modules\Notifications\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationsBulkUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $type;

    public function __construct($userId, $type = null)
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'notifications.bulk_updated';
    }

    public function broadcastWith()
    {
        return [
            'type' => 'bulk_update',
            'payload' => [
                'user_id' => $this->userId,
                'notification_type' => $this->type,
                'timestamp' => now()->toIso8601String(),
            ]
        ];
    }
}
