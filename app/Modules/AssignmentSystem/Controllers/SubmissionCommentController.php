<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\AssignmentSubmission;
use App\Modules\AssignmentSystem\Models\SubmissionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class SubmissionCommentController extends Controller
{
    /**
     * Get all comments for a submission.
     */
    public function index($submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        // Check if user has access to view comments
        $user = Auth::user();
        if ($submission->user_id !== $user->id) {
            // Check if user is instructor for this course
            $courseInstructor = $submission->assignment->course->instructors()
                ->where('user_id', $user->id)
                ->exists();

            if (!$courseInstructor && !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized to view comments'], 403);
            }
        }

        $comments = SubmissionComment::where('assignment_submission_id', $submissionId)
            ->with(['user', 'replies.user'])
            ->whereNull('parent_comment_id')
            ->orderBy('created_at')
            ->get();

        return response()->json(['comments' => $comments]);
    }

    /**
     * Add a comment to a submission.
     */
    public function store(Request $request, $submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        // Check if user has access to comment
        $user = Auth::user();
        $canComment = false;

        // Owner can comment
        if ($submission->user_id === $user->id) {
            $canComment = true;
        }

        // Instructor can comment
        $courseInstructor = $submission->assignment->course->instructors()
            ->where('user_id', $user->id)
            ->exists();

        if ($courseInstructor || $user->hasRole('admin')) {
            $canComment = true;
        }

        if (!$canComment) {
            return response()->json(['error' => 'Unauthorized to comment on this submission'], 403);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'parent_comment_id' => 'nullable|exists:submission_comments,id',
            'is_private' => 'boolean',
            'attachment' => 'nullable|file|max:10240',
            'comment_location' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle attachment if provided
        $attachmentData = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('comments/' . $submissionId);
            $attachmentData = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ];
        }

        $comment = SubmissionComment::create([
            'assignment_submission_id' => $submissionId,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_comment_id' => $request->parent_comment_id,
            'is_private' => $request->is_private ?? false,
            'attachment' => $attachmentData,
            'comment_location' => $request->comment_location,
        ]);

        return response()->json(['comment' => $comment->load('user')], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $comment = SubmissionComment::findOrFail($id);

        // Ensure user is updating their own comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to edit this comment'], 403);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'is_private' => 'boolean',
            'comment_location' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment->update([
            'comment' => $request->comment,
            'is_private' => $request->is_private ?? $comment->is_private,
            'comment_location' => $request->comment_location ?? $comment->comment_location,
        ]);

        return response()->json(['comment' => $comment]);
    }

    /**
     * Delete a comment.
     */
    public function destroy($id)
    {
        $comment = SubmissionComment::findOrFail($id);

        // Check if user has permission to delete
        $user = Auth::user();
        if ($comment->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized to delete this comment'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}
