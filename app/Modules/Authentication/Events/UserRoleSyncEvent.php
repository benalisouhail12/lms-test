<?php

namespace App\Modules\Authentication\Events;

use App\Modules\Authentication\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserRoleSyncEvent
{
    use Dispatchable, SerializesModels;

    public User $user;
    public array $addedRoles;
    public array $removedRoles;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param array $addedRoles
     * @param array $removedRoles
     */
    public function __construct(User $user, array $addedRoles, array $removedRoles)
    {
        $this->user = $user;
        $this->addedRoles = $addedRoles;
        $this->removedRoles = $removedRoles;
    }
}
