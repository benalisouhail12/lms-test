<?php
namespace App\Modules\StudentPortal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CourseManagement\Models\Course;
use Illuminate\Http\Request;
use App\Modules\StudentPortal\Services\AchievementService;

class AchievementController extends Controller
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->achievementService->getAllAchievements($request->user())
        ]);
    }

    public function badges(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->achievementService->getUserBadges($request->user())
        ]);
    }

    public function certificates(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->achievementService->getUserCertificates($request->user())
        ]);
    }

    public function downloadCertificate(Request $request, Course $course)
    {
        return $this->achievementService->generateCertificatePdf($request->user(), $course);
    }
}
