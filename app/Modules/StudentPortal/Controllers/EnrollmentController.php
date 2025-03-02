<?php
namespace App\Modules\StudentPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use Illuminate\Http\Request;
use App\Modules\StudentPortal\Services\EnrollmentService;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    public function availableCourses(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->getAvailableCourses($request->user())
        ]);
    }

    public function enrolledCourses(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->getEnrolledCourses($request->user())
        ]);
    }

    public function enrollInCourse(Request $request, Course $course)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->enrollUserInCourse($request->user(), $course)
        ]);
    }

    public function dropCourse(Request $request, Course $course)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->dropUserFromCourse($request->user(), $course)
        ]);
    }

    public function availableLearningPaths(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->getAvailableLearningPaths($request->user())
        ]);
    }

    public function enrollInLearningPath(Request $request, LearningPath $path)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrollmentService->enrollUserInLearningPath($request->user(), $path)
        ]);
    }
}
