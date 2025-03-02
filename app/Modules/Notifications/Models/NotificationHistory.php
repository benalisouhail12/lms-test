<?php
namespace App\Modules\Notifications\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'channel',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // Relationships
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
