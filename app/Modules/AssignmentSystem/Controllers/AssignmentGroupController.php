<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\AssignmentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AssignmentGroupController extends Controller
{
    /**
     * Get all groups for an assignment.
     */
    public function index($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Ensure the assignment allows group submissions
        if (!$assignment->is_group_assignment) {
            return response()->json(['error' => 'This assignment does not support group submissions'], 400);
        }

        $groups = AssignmentGroup::where('assignment_id', $assignmentId)
            ->with(['members.user'])
            ->get();

        return response()->json(['groups' => $groups]);
    }

    /**
     * Create a new group.
     */
    public function store(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Ensure the assignment allows group submissions
        if (!$assignment->is_group_assignment) {
            return response()->json(['error' => 'This assignment does not support group submissions'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'members.*.user_id' => 'required|exists:users,id',
            'members.*.is_leader' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the group
        $group = AssignmentGroup::create([
            'name' => $request->name,
            'assignment_id' => $assignmentId,
        ]);

        // Add members
        foreach ($request->members as $member) {
            $group->members()->create([
                'user_id' => $member['user_id'],
                'is_leader' => $member['is_leader'] ?? false,
            ]);
        }

        return response()->json(['group' => $group->load('members.user')], 201);
    }

    /**
     * Update a group.
     */
    public function update(Request $request, $id)
    {
        $group = AssignmentGroup::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'members' => 'array',
            'members.*.user_id' => 'exists:users,id',
            'members.*.is_leader' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update group name
        if ($request->has('name')) {
            $group->name = $request->name;
            $group->save();
        }

        // Update members if provided
        if ($request->has('members')) {
            // Remove existing members
            $group->members()->delete();

            // Add new members
            foreach ($request->members as $member) {
                $group->members()->create([
                    'user_id' => $member['user_id'],
                    'is_leader' => $member['is_leader'] ?? false,
                ]);
            }
        }

        return response()->json(['group' => $group->load('members.user')]);
    }

    /**
     * Delete a group.
     */
    public function destroy($id)
    {
        $group = AssignmentGroup::findOrFail($id);

        // Check if the group has submissions
        if ($group->submissions()->exists()) {
            return response()->json(['error' => 'Cannot delete group with existing submissions'], 400);
        }

        $group->members()->delete();
        $group->delete();

        return response()->json(null, 204);
    }
}
