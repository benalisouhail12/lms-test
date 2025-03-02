<?php
namespace App\Modules\StudentPortal\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use App\Modules\CourseManagement\Models\Lesson;
use App\Modules\CourseManagement\Models\LessonProgress;
use Carbon\Carbon;

class ProgressService
{
    /**
     * Get overview of user's progress across all enrolled courses
     *
     * @param User $user
     * @return array
     */
    public function getProgressOverview(User $user)
    {
        $enrollments = $user->courseEnrollments()
            ->with('course')
            ->get();

        $activeCourses = $enrollments->where('status', 'ACTIVE')->count();
        $completedCourses = $enrollments->where('status', 'COMPLETED')->count();
        $totalCourses = $enrollments->count();

        $recentActivity = LessonProgress::where('user_id', $user->id)
            ->with('lesson')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($progress) {
                return [
                    'lesson_id' => $progress->lesson_id,
                    'lesson_title' => $progress->lesson->title,
                    'status' => $progress->status,
                    'viewed_at' => $progress->viewed_at,
                    'completed_at' => $progress->completed_at,
                    'updated_at' => $progress->updated_at,
                ];
            });

        return [
            'courses' => [
                'active' => $activeCourses,
                'completed' => $completedCourses,
                'total' => $totalCourses,
            ],
            'average_progress' => $enrollments->avg('progress_percentage'),
            'recent_activity' => $recentActivity,
        ];
    }

    /**
     * Get detailed progress for a specific course
     *
     * @param User $user
     * @param Course $course
     * @return array
     */
    public function getCourseProgress(User $user, Course $course)
    {
        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return ['error' => 'User is not enrolled in this course'];
        }

        $sections = $course->sections()
            ->with(['lessons' => function ($query) use ($user) {
                $query->with(['lessonProgress' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }]);
            }])
            ->orderBy('position')
            ->get();

        return [
            'enrollment' => [
                'status' => $enrollment->status,
                'progress_percentage' => $enrollment->progress_percentage,
                'enrolled_at' => $enrollment->enrolled_at,
                'completed_at' => $enrollment->completed_at,
            ],
            'sections' => $sections->map(function ($section) {
                $totalLessons = $section->lessons->count();
                $completedLessons = $section->lessons->filter(function ($lesson) {
                    return $lesson->lessonProgress && $lesson->lessonProgress->status === 'COMPLETED';
                })->count();

                $sectionProgress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'progress_percentage' => $sectionProgress,
                    'completed_lessons' => $completedLessons,
                    'total_lessons' => $totalLessons,
                    'lessons' => $section->lessons->map(function ($lesson) {
                        return [
                            'id' => $lesson->id,
                            'title' => $lesson->title,
                            'estimated_duration' => $lesson->estimated_duration,
                            'lesson_type' => $lesson->lesson_type,
                            'progress' => $lesson->lessonProgress ? [
                                'status' => $lesson->lessonProgress->status,
                                'viewed_at' => $lesson->lessonProgress->viewed_at,
                                'completed_at' => $lesson->lessonProgress->completed_at,
                                'time_spent' => $lesson->lessonProgress->time_spent,
                            ] : null,
                        ];
                    }),
                ];
            }),
        ];
    }

    /**
     * Get progress for a learning path
     *
     * @param User $user
     * @param LearningPath $path
     * @return array
     */
    public function getLearningPathProgress(User $user, LearningPath $path)
    {
        $pathCourses = $path->courses()
            ->orderBy('course_learning_path.position')
            ->get();

        $courseProgress = [];
        $completedCourses = 0;

        foreach ($pathCourses as $course) {
            $enrollment = $user->courseEnrollments()
                ->where('course_id', $course->id)
                ->first();

            $status = $enrollment ? $enrollment->status : 'NOT_ENROLLED';
            $progress = $enrollment ? $enrollment->progress_percentage : 0;

            if ($status === 'COMPLETED') {
                $completedCourses++;
            }

            $courseProgress[] = [
                'course_id' => $course->id,
                'title' => $course->title,
                'status' => $status,
                'progress_percentage' => $progress,
            ];
        }

        $pathProgressPercentage = $pathCourses->count() > 0
            ? ($completedCourses / $pathCourses->count()) * 100
            : 0;

        return [
            'learning_path' => [
                'id' => $path->id,
                'title' => $path->title,
                'description' => $path->description,
            ],
            'progress_percentage' => $pathProgressPercentage,
            'completed_courses' => $completedCourses,
            'total_courses' => $pathCourses->count(),
            'courses' => $courseProgress,
        ];
    }

    /**
     * Mark a lesson as complete
     *
     * @param User $user
     * @param Lesson $lesson
     * @return LessonProgress
     */
    public function markLessonAsComplete(User $user, Lesson $lesson)
    {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => 'NOT_STARTED', 'time_spent' => 0]
        );

        $progress->status = 'COMPLETED';
        $progress->completed_at = Carbon::now();
        $progress->save();

        // Update course progress
        $this->updateCourseProgress($user, $lesson->section->course);

        return $progress;
    }

    /**
     * Track time spent on a lesson
     *
     * @param User $user
     * @param Lesson $lesson
     * @param int $secondsSpent
     * @return LessonProgress
     */
    public function trackLessonTimeSpent(User $user, Lesson $lesson, int $secondsSpent)
    {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => 'NOT_STARTED', 'time_spent' => 0]
        );

        if (!$progress->viewed_at) {
            $progress->viewed_at = Carbon::now();
            $progress->status = 'IN_PROGRESS';
        }

        $progress->time_spent += $secondsSpent;
        $progress->save();

        return $progress;
    }
/**
     * Update course progress percentage
     *
     * @param User $user
     * @param Course $course
     * @return void
     */
    private function updateCourseProgress(User $user, Course $course)
    {
        // Get all lessons in the course
        $lessonIds = $course->sections()
            ->with('lessons')
            ->get()
            ->pluck('lessons')
            ->flatten()
            ->pluck('id');

        $totalLessons = $lessonIds->count();

        if ($totalLessons === 0) {
            return;
        }

        // Count completed lessons
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->where('status', 'COMPLETED')
            ->count();

        // Calculate progress percentage
        $progressPercentage = ($completedLessons / $totalLessons) * 100;

        // Update enrollment record
        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->progress_percentage = $progressPercentage;

            // If progress is 100%, mark course as completed
            if ($progressPercentage >= 100 && $enrollment->status === 'ACTIVE') {
                $enrollment->status = 'COMPLETED';
                $enrollment->completed_at = Carbon::now();
            }

            $enrollment->save();
        }
    }
}
