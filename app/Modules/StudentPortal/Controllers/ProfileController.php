<?php
namespace app\Modules\StudentPortal\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\StudentPortal\Services\ProfileService;


class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->profileService->getUserProfile($request->user())
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
       /*      'phone' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:1000', */
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->profileService->updateProfile($request->user(), $request->all())
        ]);
    }

    public function academicHistory(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->profileService->getAcademicHistory($request->user())
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|max:2048',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->profileService->updateAvatar($request->user(), $request->file('avatar'))
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $this->validate($request, [
            'preferences' => 'required|array',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->profileService->updatePreferences($request->user(), $request->input('preferences'))
        ]);
    }
}
