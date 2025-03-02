<?php
namespace App\Modules\Notifications\Services;

use App\Modules\Notifications\Models\Notification;

class GroupingService
{
    public function determineGroupId(array $data)
    {
        // Different grouping strategies based on notification type
        switch ($data['type']) {
            case 'message':
                return $this->groupMessages($data);

            case 'update':
                return $this->groupUpdates($data);

            case 'activity':
                return $this->groupActivity($data);

            default:
                return null; // No grouping
        }
    }

    protected function groupMessages(array $data)
    {
        // If meta_data contains a conversation_id, group by that
        if (isset($data['meta_data']['conversation_id'])) {
            return 'conversation_' . $data['meta_data']['conversation_id'];
        }

        // If from the same sender within 30 minutes, group together
        if (isset($data['meta_data']['sender_id'])) {
            $senderId = $data['meta_data']['sender_id'];

            $recentNotification = Notification::where('user_id', $data['user_id'])
                ->where('type', 'message')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->whereJsonContains('meta_data->sender_id', $senderId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentNotification && $recentNotification->group_id) {
                return $recentNotification->group_id;
            }

            return 'sender_' . $senderId . '_' . now()->format('YmdHi');
        }

        return null;
    }

    protected function groupUpdates(array $data)
    {
        // Group updates by entity type and ID if available
        if (isset($data['meta_data']['entity_type']) && isset($data['meta_data']['entity_id'])) {
            $entityType = $data['meta_data']['entity_type'];
            $entityId = $data['meta_data']['entity_id'];

            // For updates on the same entity within 1 hour, group them
            $recentNotification = Notification::where('user_id', $data['user_id'])
                ->where('type', 'update')
                ->where('created_at', '>=', now()->subHour())
                ->whereJsonContains('meta_data->entity_type', $entityType)
                ->whereJsonContains('meta_data->entity_id', $entityId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentNotification && $recentNotification->group_id) {
                return $recentNotification->group_id;
            }

            return 'entity_' . $entityType . '_' . $entityId . '_' . now()->format('YmdH');
        }

        return null;
    }

    protected function groupActivity(array $data)
    {
        // Group activity by type for the same day
        if (isset($data['meta_data']['activity_type'])) {
            $activityType = $data['meta_data']['activity_type'];
            return 'activity_' . $activityType . '_' . now()->format('Ymd');
        }

        return null;
    }
}
