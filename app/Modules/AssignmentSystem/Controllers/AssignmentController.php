<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for a course.
     */
    public function index(Request $request, $courseId)
    {
        $assignments = Assignment::where('course_id', $courseId)
            ->with(['versions', 'criteria'])
            ->orderBy('due_date')
            ->get();

        return response()->json(['assignments' => $assignments]);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'nullable|date',
            'available_from' => 'nullable|date',
            'max_points' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assignment = Assignment::create($request->all());

        // Create initial version
        $assignment->versions()->create([
            'version_number' => 1,
            'changes_description' => 'Initial version',
            'created_by' => Auth::id(),
        ]);

        return response()->json(['assignment' => $assignment], 201);
    }

    /**
     * Display the specified assignment.
     */
    public function show($id)
    {
        $assignment = Assignment::with(['versions', 'criteria', 'course'])
            ->findOrFail($id);

        return response()->json(['assignment' => $assignment]);
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'due_date' => 'nullable|date',
            'available_from' => 'nullable|date',
            'max_points' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assignment->update($request->all());

        // Create new version
        $latestVersion = $assignment->versions()->max('version_number');
        $assignment->versions()->create([
            'version_number' => $latestVersion + 1,
            'changes_description' => $request->changes_description ?? 'Updated assignment',
            'content_diff' => $request->content_diff ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['assignment' => $assignment]);
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return response()->json(null, 204);
    }

    /**
     * Publish the specified assignment.
     */
    public function publish($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->status = 'PUBLISHED';
        $assignment->save();

        return response()->json(['assignment' => $assignment]);
    }

    /**
     * Archive the specified assignment.
     */
    public function archive($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->status = 'ARCHIVED';
        $assignment->save();

        return response()->json(['assignment' => $assignment]);
    }
}










