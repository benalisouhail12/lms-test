<?php
namespace App\Modules\Notifications\Mail;

use App\Modules\Notifications\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SingleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        $preferencesUrl = url('/notifications/preferences');
        $unsubscribeUrl = url('/notifications/unsubscribe?token=' . encrypt($this->notification->user_id));

        // Determine if there's an action URL in the meta_data
        $actionUrl = $this->notification->meta_data['action_url'] ?? null;
        $actionText = $this->notification->meta_data['action_text'] ?? 'View Details';

        return $this->subject($this->notification->title)
                   ->view('notifications::emails.single_notification')
                   ->with([
                       'title' => $this->notification->title,
                       'message' => $this->notification->message,
                       'actionUrl' => $actionUrl,
                       'actionText' => $actionText,
                       'preferencesUrl' => $preferencesUrl,
                       'unsubscribeUrl' => $unsubscribeUrl,
                       'createdAt' => $this->notification->created_at->format('F j, Y, g:i a'),
                   ]);
    }
}
