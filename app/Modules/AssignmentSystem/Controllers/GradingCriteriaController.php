<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\GradingCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class GradingCriteriaController extends Controller
{
    /**
     * Get all grading criteria for an assignment.
     */
    public function index($assignmentId)
    {
        $criteria = GradingCriteria::where('assignment_id', $assignmentId)
            ->orderBy('id')
            ->get();

        return response()->json(['criteria' => $criteria]);
    }

    /**
     * Store a new grading criterion.
     */
    public function store(Request $request, $assignmentId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_points' => 'required|integer|min:0',
            'weight' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assignment = Assignment::findOrFail($assignmentId);

        $criterion = $assignment->criteria()->create($request->all());

        return response()->json(['criterion' => $criterion], 201);
    }

    /**
     * Update a grading criterion.
     */
    public function update(Request $request, $id)
    {
        $criterion = GradingCriteria::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'max_points' => 'integer|min:0',
            'weight' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $criterion->update($request->all());

        return response()->json(['criterion' => $criterion]);
    }

    /**
     * Remove a grading criterion.
     */
    public function destroy($id)
    {
        $criterion = GradingCriteria::findOrFail($id);
        $criterion->delete();

        return response()->json(null, 204);
    }
}
