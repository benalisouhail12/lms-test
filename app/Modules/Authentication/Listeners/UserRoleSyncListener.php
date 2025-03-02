<?php
namespace App\Modules\Authentication\Listeners;

use App\Modules\Authentication\Events\UserRoleSyncEvent;
use Illuminate\Support\Facades\Log;

class UserRoleSyncListener
{
    /**
     * Handle the event.
     *
     * @param UserRoleSyncEvent $event
     * @return void
     */
    public function handle(UserRoleSyncEvent $event)
    {
        $user = $event->user;
        $addedRoles = $event->addedRoles;
        $removedRoles = $event->removedRoles;

        Log::info("User {$user->email} roles synced", [
            'added' => $addedRoles,
            'removed' => $removedRoles,
            'current' => $user->getRoleNames()->toArray(),
        ]);

    }
}
