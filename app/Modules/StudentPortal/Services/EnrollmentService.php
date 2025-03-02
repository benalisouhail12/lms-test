<?php
namespace App\Modules\StudentPortal\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\Enrollment;
use App\Modules\CourseManagement\Models\LearningPath;
use Carbon\Carbon;

class EnrollmentService
{
    /**
     * Get courses available for enrollment
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCourses(User $user)
    {
        // Get IDs of courses the user is already enrolled in
        $enrolledCourseIds = $user->courseEnrollments()->pluck('course_id');

        // Get courses that are published and not already enrolled
        return Course::where('status', 'PUBLISHED')
            ->whereNotIn('id', $enrolledCourseIds)
            ->with('department', 'program')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'short_description' => $course->short_description,
                    'course_type' => $course->course_type,
                    'level' => $course->level,
                    'duration_in_weeks' => $course->duration_in_weeks,
                    'credit_hours' => $course->credit_hours,
                    'department' => $course->department ? $course->department->name : null,
                    'program' => $course->program ? $course->program->name : null,
                ];
            });
    }

    /**
     * Get courses the user is enrolled in
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnrolledCourses(User $user)
    {
        return $user->courseEnrollments()
            ->with('course')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->course->id,
                    'title' => $enrollment->course->title,
                    'short_description' => $enrollment->course->short_description,
                    'status' => $enrollment->status,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
                ];
            });
    }

    /**
     * Enroll a user in a course
     *
     * @param User $user
     * @param Course $course
     * @return Enrollment
     */
    public function enrollUserInCourse(User $user, Course $course)
    {
        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return $existingEnrollment;
        }

        // Create a new enrollment
        return Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'ACTIVE',
            'enrolled_at' => Carbon::now(),
            'progress_percentage' => 0
        ]);
    }

    /**
     * Drop a user from a course
     *
     * @param User $user
     * @param Course $course
     * @return bool
     */
    public function dropUserFromCourse(User $user, Course $course)
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update(['status' => 'DROPPED']);
    }

    /**
     * Get learning paths available for enrollment
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableLearningPaths(User $user)
    {
        return LearningPath::where('status', 'PUBLISHED')
            ->with(['courses' => function ($query) {
                $query->select('courses.id', 'courses.title', 'courses.level', 'courses.duration_in_weeks')
                    ->orderBy('course_learning_path.position');
            }])
            ->get()
            ->map(function ($path) {
                return [
                    'id' => $path->id,
                    'title' => $path->title,
                    'description' => $path->description,
                    'courses_count' => $path->courses->count(),
                    'estimated_duration_weeks' => $path->courses->sum('duration_in_weeks'),
                    'courses' => $path->courses->map(function ($course) {
                        return [
                            'id' => $course->id,
                            'title' => $course->title,
                            'level' => $course->level,
                        ];
                    }),
                ];
            });
    }

    /**
     * Enroll a user in all courses of a learning path
     *
     * @param User $user
     * @param LearningPath $path
     * @return array
     */
    public function enrollUserInLearningPath(User $user, LearningPath $path)
    {
        $enrollments = [];

        foreach ($path->courses()->orderBy('course_learning_path.position')->get() as $course) {
            $enrollments[] = $this->enrollUserInCourse($user, $course);
        }

        return $enrollments;
    }
}
