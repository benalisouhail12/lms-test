<?php

namespace App\Modules\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationDigest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $notifications;
    public $frequency;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $notifications, $frequency)
    {
        $this->user = $user;
        $this->notifications = $notifications;
        $this->frequency = $frequency;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Notification Digest')
                    ->view('Notifications::emails.notification_digest')
                    ->with([
                        'user' => $this->user,
                        'notifications' => $this->notifications,
                        'frequency' => $this->frequency,
                    ]);
    }
}
