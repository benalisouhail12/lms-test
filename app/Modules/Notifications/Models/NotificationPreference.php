<?php
namespace App\Modules\Notifications\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'channels',
        'notification_types',
        'quiet_hours',
        'digest_settings',
    ];

    protected $casts = [
        'channels' => 'array',
        'notification_types' => 'array',
        'quiet_hours' => 'array',
        'digest_settings' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isChannelEnabled($channel)
    {
        return isset($this->channels[$channel]['enabled']) &&
               $this->channels[$channel]['enabled'] === true;
    }

    public function isTypeEnabled($type)
    {
        return isset($this->notification_types[$type]['enabled']) &&
               $this->notification_types[$type]['enabled'] === true;
    }

    public function getEnabledChannelsForType($type)
    {
        if (!$this->isTypeEnabled($type)) {
            return [];
        }

        return $this->notification_types[$type]['channels'] ?? [];
    }

    public function isInQuietHours()
    {
        if (!isset($this->quiet_hours['enabled']) || !$this->quiet_hours['enabled']) {
            return false;
        }

        $now = now();
        if (isset($this->quiet_hours['timezone'])) {
            $now = $now->setTimezone($this->quiet_hours['timezone']);
        }

        $currentTime = $now->format('H:i');
        $startTime = $this->quiet_hours['start'];
        $endTime = $this->quiet_hours['end'];

        if ($startTime < $endTime) {
            return $currentTime >= $startTime && $currentTime <= $endTime;
        } else {
            // Handle overnight quiet hours (e.g., 22:00 - 06:00)
            return $currentTime >= $startTime || $currentTime <= $endTime;
        }
    }
}



