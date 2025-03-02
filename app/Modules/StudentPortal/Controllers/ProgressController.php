<?php
namespace App\Modules\StudentPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use App\Modules\CourseManagement\Models\Lesson;
use Illuminate\Http\Request;
use App\Modules\StudentPortal\Services\ProgressService;


class ProgressController extends Controller
{
    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function overview(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->progressService->getProgressOverview($request->user())
        ]);
    }

    public function courseProgress(Request $request, Course $course)
    {
        return response()->json([
            'success' => true,
            'data' => $this->progressService->getCourseProgress($request->user(), $course)
        ]);
    }

    public function learningPathProgress(Request $request, LearningPath $path)
    {
        return response()->json([
            'success' => true,
            'data' => $this->progressService->getLearningPathProgress($request->user(), $path)
        ]);
    }

    public function markLessonComplete(Request $request, Lesson $lesson)
    {
        return response()->json([
            'success' => true,
            'data' => $this->progressService->markLessonAsComplete($request->user(), $lesson)
        ]);
    }

    public function trackTimeSpent(Request $request, Lesson $lesson)
    {
        $this->validate($request, [
            'seconds_spent' => 'required|integer|min:1',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->progressService->trackLessonTimeSpent(
                $request->user(),
                $lesson,
                $request->input('seconds_spent')
            )
        ]);
    }
}
