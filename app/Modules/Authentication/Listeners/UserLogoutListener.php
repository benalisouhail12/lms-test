<?php
namespace App\Modules\Authentication\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class UserLogoutListener
{
    /**
     * Handle the event.
     *
     * @param Logout $event
     * @return void
     */
    public function handle(Logout $event)
    {
        if ($event->user) {
            Log::info("User logged out: {$event->user->email}");
        }
    }
}
