<?php
namespace App\Modules\StudentPortal\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Enrollment;
use App\Modules\CourseManagement\Models\LessonProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get overall performance metrics for a user
     *
     * @param User $user
     * @return array
     */
    public function getOverallPerformance(User $user)
    {
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->get();

        $activeCourses = $enrollments->where('status', 'ACTIVE');
        $completedCourses = $enrollments->where('status', 'COMPLETED');

        // Calculate average time to complete courses
        $avgCompletionTime = 0;
        if ($completedCourses->count() > 0) {
            $avgCompletionTime = $completedCourses->map(function ($enrollment) {
                $start = Carbon::parse($enrollment->enrolled_at);
                $end = Carbon::parse($enrollment->completed_at);
                return $end->diffInDays($start);
            })->avg();
        }

        return [
            'courses_completed' => $completedCourses->count(),
            'courses_in_progress' => $activeCourses->count(),
            'courses_dropped' => $enrollments->where('status', 'DROPPED')->count(),
            'average_progress' => $activeCourses->avg('progress_percentage') ?? 0,
            'average_completion_time_days' => round($avgCompletionTime, 1),
            'total_learning_time_hours' => $this->getTotalLearningTime($user) / 3600,
        ];
    }

    /**
     * Get performance on activities
     *
     * @param User $user
     * @return array
     */
    public function getActivitiesPerformance(User $user)
    {
        // This would involve querying the activities table to get scores and completion rates
        // For the purposes of this example, we'll return a placeholder

        return [
            'activities_completed' => 0,
            'activities_pending' => 0,
            'average_score' => 0,
            'highest_score' => 0,
            'activities_by_type' => [],
        ];
    }

    /**
     * Get assessment results
     *
     * @param User $user
     * @return array
     */
    public function getAssessmentsResults(User $user)
    {
        // This would involve querying the assessment results
        // For the purposes of this example, we'll return a placeholder

        return [
            'assessments_completed' => 0,
            'average_score' => 0,
            'top_performing_subjects' => [],
            'areas_for_improvement' => [],
        ];
    }

    /**
     * Get completion rate metrics
     *
     * @param User $user
     * @return array
     */
    public function getCompletionRate(User $user)
    {
        $enrollments = Enrollment::where('user_id', $user->id)->get();
        $totalEnrollments = $enrollments->count();

        if ($totalEnrollments === 0) {
            return [
                'overall_completion_rate' => 0,
                'course_completion_trend' => [],
            ];
        }

        $completedEnrollments = $enrollments->where('status', 'COMPLETED')->count();
        $completionRate = ($completedEnrollments / $totalEnrollments) * 100;

        // Get monthly completion trend for the past 6 months
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $completionTrend = Enrollment::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->where('completed_at', '>=', $sixMonthsAgo)
            ->selectRaw('DATE_FORMAT(completed_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return [
            'overall_completion_rate' => $completionRate,
            'course_completion_trend' => $completionTrend,
        ];
    }

    /**
     * Get engagement metrics
     *
     * @param User $user
     * @return array
     */
    public function getEngagementMetrics(User $user)
    {
        // Calculate login frequency and study patterns
        $lessonProgress = LessonProgress::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Daily activity distribution (hours of the day)
        $hourlyDistribution = $lessonProgress
            ->groupBy(function ($item) {
                return Carbon::parse($item->updated_at)->format('H');
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->toArray();

        // Weekly activity distribution (days of the week)
        $weeklyDistribution = $lessonProgress
            ->groupBy(function ($item) {
                return Carbon::parse($item->updated_at)->format('w'); // 0 (Sunday) to 6 (Saturday)
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->toArray();

        return [
            'total_sessions' => $lessonProgress->count(),
            'average_session_length_minutes' => $this->getAverageSessionLength($user),
            'last_activity' => $lessonProgress->first() ? $lessonProgress->first()->updated_at : null,
            'hourly_activity_distribution' => $hourlyDistribution,
            'weekly_activity_distribution' => $weeklyDistribution,
        ];
    }

    /**
     * Get total learning time in seconds
     *
     * @param User $user
     * @return int
     */
    private function getTotalLearningTime(User $user)
    {
        return LessonProgress::where('user_id', $user->id)
            ->sum('time_spent');
    }

    /**
     * Get average session length in minutes
     *
     * @param User $user
     * @return float
     */
    private function getAverageSessionLength(User $user)
    {
        // For this example, we'll assume a session is a continuous period of activity
        // This is a simplified version and would require more complex logic in a real application

        $avgTimeSpent = LessonProgress::where('user_id', $user->id)
            ->avg('time_spent');

        return $avgTimeSpent ? round($avgTimeSpent / 60, 1) : 0;
    }
}
