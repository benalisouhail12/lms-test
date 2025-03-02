<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AssignmentExtensionController extends Controller
{
    /**
     * Grant an extension for a student.
     */
    public function grantExtension(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Check if user has permission to grant extensions
        $user = Auth::user();
        $courseInstructor = $assignment->course->instructors()
            ->where('user_id', $user->id)
            ->exists();

        if (!$courseInstructor && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized to grant extensions'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'extended_due_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create or update extension
        $extension = $assignment->extensions()->updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'extended_due_date' => $request->extended_due_date,
                'reason' => $request->reason,
                'granted_by' => $user->id,
            ]
        );

        return response()->json(['extension' => $extension], 201);
    }

    /**
     * Revoke an extension.
     */
    public function revokeExtension($assignmentId, $userId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Check if user has permission to manage extensions
        $user = Auth::user();
        $courseInstructor = $assignment->course->instructors()
            ->where('user_id', $user->id)
            ->exists();

        if (!$courseInstructor && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized to manage extensions'], 403);
        }

        // Find and delete the extension
        $extension = $assignment->extensions()
            ->where('user_id', $userId)
            ->first();

        if (!$extension) {
            return response()->json(['error' => 'Extension not found'], 404);
        }

        $extension->delete();

        return response()->json(null, 204);
    }

    /**
     * List all extensions for an assignment.
     */
    public function listExtensions($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Check if user has permission to view extensions
        $user = Auth::user();
        $courseInstructor = $assignment->course->instructors()
            ->where('user_id', $user->id)
            ->exists();

        if (!$courseInstructor && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized to view extensions'], 403);
        }

        $extensions = $assignment->extensions()
            ->with(['user', 'grantedBy'])
            ->get();

        return response()->json(['extensions' => $extensions]);
    }
}
