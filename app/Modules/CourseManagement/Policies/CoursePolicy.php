<?php
namespace App\Modules\CourseManagement\Policies;

use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;

class CoursePolicy
{
    public function create(User $user)
    {
        return true; // Allow all users to create for testing
    }
}
