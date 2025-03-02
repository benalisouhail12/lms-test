<?php

namespace App\Modules\StudentPortal\Services;

use App\Modules\Authentication\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    /**
     * Get the user's profile
     *
     * @param User $user
     * @return array
     */
    public function getUserProfile(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            /* 'avatar' => $user->avatar_url,
            'phone' => $user->phone,
            'bio' => $user->bio,
            'preferences' => $user->preferences, */
            'created_at' => $user->created_at
        ];
    }

    /**
     * Update the user's profile
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    /**
     * Get the user's academic history
     *
     * @param User $user
     * @return array
     */
    public function getAcademicHistory(User $user)
    {
        $enrollments = $user->courseEnrollments()
            ->with('course')
            ->where('status', 'COMPLETED')
            ->get();

        return $enrollments->map(function ($enrollment) {
            return [
                'course' => [
                    'id' => $enrollment->course->id,
                    'title' => $enrollment->course->title,
                    'description' => $enrollment->course->short_description,
                    'level' => $enrollment->course->level,
                    'credit_hours' => $enrollment->course->credit_hours,
                ],
                'completed_at' => $enrollment->completed_at,
                'progress_percentage' => $enrollment->progress_percentage,
            ];
        });
    }

    /**
     * Update the user's avatar
     *
     * @param User $user
     * @param UploadedFile $avatar
     * @return User
     */
    public function updateAvatar(User $user, UploadedFile $avatar)
    {
        // Delete previous avatar if exists
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        // Store the new avatar
        $path = $avatar->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return [
            'avatar_url' => Storage::url($path)
        ];
    }

    /**
     * Update the user's preferences
     *
     * @param User $user
     * @param array $preferences
     * @return User
     */
    public function updatePreferences(User $user, array $preferences)
    {
        $user->preferences = $preferences;
        $user->save();

        return $user;
    }
}









