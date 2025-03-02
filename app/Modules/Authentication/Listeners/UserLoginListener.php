<?php
namespace App\Modules\Authentication\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class UserLoginListener
{
    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        Log::info("User logged in: {$user->email}");
    }
}
