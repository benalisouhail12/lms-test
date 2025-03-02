<?php
namespace App\Modules\StudentPortal\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;
use Illuminate\Support\Facades\Storage;
use PDF;

class AchievementService
{
    /**
     * Get all achievements for a user
     *
     * @param User $user
     * @return array
     */
    public function getAllAchievements(User $user)
    {
        $badges = $this->getUserBadges($user);
        $certificates = $this->getUserCertificates($user);

        return [
            'badges' => $badges,
            'certificates' => $certificates,
        ];
    }

    /**
     * Get badges earned by a user
     *
     * @param User $user
     * @return array
     */
    public function getUserBadges(User $user)
    {
        // This would typically involve querying a badges or achievements table
        // For this example, we'll generate some badges based on course completions

        $completedCourses = $user->courseEnrollments()
            ->where('status', 'COMPLETED')
            ->count();

        $badges = [];

        // First course completion badge
        if ($completedCourses >= 1) {
            $badges[] = [
                'id' => 'first_course',
                'title' => 'First Steps',
                'description' => 'Completed your first course',
                'icon' => 'badge-first-course',
                'earned_at' => $user->courseEnrollments()
                    ->where('status', 'COMPLETED')
                    ->orderBy('completed_at')
                    ->first()
                    ->completed_at,
            ];
        }

        // 5 courses completion badge
        if ($completedCourses >= 5) {
            $badges[] = [
                'id' => 'five_courses',
                'title' => 'Dedicated Learner',
                'description' => 'Completed 5 courses',
                'icon' => 'badge-five-courses',
                'earned_at' => $user->courseEnrollments()
                    ->where('status', 'COMPLETED')
                    ->orderBy('completed_at')
                    ->skip(4)
                    ->first()
                    ->completed_at,
            ];
        }

        // 10 courses completion badge
        if ($completedCourses >= 10) {
            $badges[] = [
                'id' => 'ten_courses',
                'title' => 'Master Learner',
                'description' => 'Completed 10 courses',
                'icon' => 'badge-ten-courses',
                'earned_at' => $user->courseEnrollments()
                    ->where('status', 'COMPLETED')
                    ->orderBy('completed_at')
                    ->skip(9)
                    ->first()
                    ->completed_at,
            ];
        }

        return $badges;
    }

    /**
     * Get certificates earned by a user
     *
     * @param User $user
     * @return array
     */
    public function getUserCertificates(User $user)
    {
        return $user->courseEnrollments()
            ->with('course')
            ->where('status', 'COMPLETED')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'course_id' => $enrollment->course_id,
                    'course_title' => $enrollment->course->title,
                    'earned_at' => $enrollment->completed_at,
                    'download_url' => route('api.student-portal.achievements.certificate.download', ['course' => $enrollment->course_id]),
                ];
            })
            ->toArray();
    }

    /**
     * Generate a PDF certificate for a completed course
     *
     * @param User $user
     * @param Course $course
     * @return \Illuminate\Http\Response
     */
    public function generateCertificatePdf(User $user, Course $course)
    {
        // Check if user has completed the course
        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', 'COMPLETED')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'error' => 'You have not completed this course yet.'
            ], 403);
        }

        // Generate certificate (using a PDF library like barryvdh/laravel-dompdf)
        $data = [
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'course_title' => $course->title,
            'completion_date' => $enrollment->completed_at->format('F d, Y'),
            'certificate_id' => 'CERT-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = PDF::loadView('student-portal::certificates.course-completion', $data);

        return $pdf->download($course->slug . '-certificate.pdf');
    }
}
