<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\AssignmentSubmission;
use App\Modules\AssignmentSystem\Models\PlagiarismReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AssignmentSubmissionController extends Controller
{
    /**
     * Get all submissions for an assignment.
     */
    public function index($assignmentId)
    {
        $submissions = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->with(['user', 'grade', 'versions', 'plagiarismReport'])
            ->get();

        return response()->json(['submissions' => $submissions]);
    }

    /**
     * Get a specific student's submissions for an assignment.
     */
    public function studentSubmissions($assignmentId, $userId)
    {
        $submissions = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', $userId)
            ->with(['versions', 'grade', 'plagiarismReport'])
            ->orderBy('attempt_number', 'desc')
            ->get();

        return response()->json(['submissions' => $submissions]);
    }

    /**
     * Submit an assignment.
     */
    public function submit(Request $request, $assignmentId)
    {
        $validator = Validator::make($request->all(), [
            'submission_text' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assignment = Assignment::findOrFail($assignmentId);
        $now = Carbon::now();

        // Check if assignment is open for submission
        if ($now->lt($assignment->available_from)) {
            return response()->json(['error' => 'Assignment is not yet available for submission'], 403);
        }

        // Check if the submission is late
        $isLate = $assignment->due_date && $now->gt($assignment->due_date);

        // Check if late submissions are allowed
        if ($isLate && !$assignment->allow_late_submissions) {
            return response()->json(['error' => 'Late submissions are not allowed for this assignment'], 403);
        }

        // Check for personal extension
        $extension = $assignment->extensions()
            ->where('user_id', Auth::id())
            ->first();

        if ($extension) {
            $isLate = $now->gt($extension->extended_due_date);
        }

        // Handle file uploads
        $fileData = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions/' . $assignmentId . '/' . Auth::id());
                $fileData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        // Check for existing submissions and determine attempt number
        $attemptNumber = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('user_id', Auth::id())
            ->max('attempt_number') + 1;

        // Check max attempts limit
        if ($assignment->max_attempts && $attemptNumber > $assignment->max_attempts) {
            return response()->json(['error' => 'Maximum number of attempts exceeded'], 403);
        }

        // Create submission
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignmentId,
            'user_id' => Auth::id(),
            'group_id' => $request->group_id,
            'submission_text' => $request->submission_text,
            'submitted_files' => $fileData,
            'attempt_number' => $attemptNumber,
            'status' => 'SUBMITTED',
            'submitted_at' => $now,
            'is_late' => $isLate,
        ]);

        // Create submission version
        $submission->versions()->create([
            'version_number' => 1,
            'submission_text' => $request->submission_text,
            'submitted_files' => $fileData,
            'submitted_at' => $now,
        ]);

        // Run plagiarism check if enabled
        if ($assignment->enable_plagiarism_detection) {
            $this->checkPlagiarism($submission);
        }

        return response()->json(['submission' => $submission], 201);
    }

    /**
     * Update a draft submission.
     */
    public function update(Request $request, $id)
    {
        $submission = AssignmentSubmission::findOrFail($id);

        // Ensure user is updating their own submission
        if ($submission->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure submission is in DRAFT status
        if ($submission->status !== 'DRAFT') {
            return response()->json(['error' => 'Only draft submissions can be updated'], 403);
        }

        $validator = Validator::make($request->all(), [
            'submission_text' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle file uploads
        $fileData = $submission->submitted_files ?? [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions/' . $submission->assignment_id . '/' . Auth::id());
                $fileData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $submission->submission_text = $request->submission_text;
        $submission->submitted_files = $fileData;
        $submission->save();

        return response()->json(['submission' => $submission]);
    }

    /**
     * Submit a draft as final submission.
     */
    public function submitDraft(Request $request, $id)
    {
        $submission = AssignmentSubmission::findOrFail($id);

        // Ensure user is submitting their own draft
        if ($submission->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure submission is in DRAFT status
        if ($submission->status !== 'DRAFT') {
            return response()->json(['error' => 'Only draft submissions can be submitted'], 403);
        }

        $assignment = $submission->assignment;
        $now = Carbon::now();

        // Check if assignment is still open for submission
        if ($now->lt($assignment->available_from)) {
            return response()->json(['error' => 'Assignment is not yet available for submission'], 403);
        }

        // Check if the submission is late
        $isLate = $assignment->due_date && $now->gt($assignment->due_date);

        // Check if late submissions are allowed
        if ($isLate && !$assignment->allow_late_submissions) {
            return response()->json(['error' => 'Late submissions are not allowed for this assignment'], 403);
        }

        // Check for personal extension
        $extension = $assignment->extensions()
            ->where('user_id', Auth::id())
            ->first();

        if ($extension) {
            $isLate = $now->gt($extension->extended_due_date);
        }

        // Update submission status
        $submission->status = 'SUBMITTED';
        $submission->submitted_at = $now;
        $submission->is_late = $isLate;
        $submission->save();

        // Create new version
        $latestVersion = $submission->versions()->max('version_number');
        $submission->versions()->create([
            'version_number' => $latestVersion + 1,
            'submission_text' => $submission->submission_text,
            'submitted_files' => $submission->submitted_files,
            'submitted_at' => $now,
        ]);

        // Run plagiarism check if enabled
        if ($assignment->enable_plagiarism_detection) {
            $this->checkPlagiarism($submission);
        }

        return response()->json(['submission' => $submission]);
    }

    /**
     * Get a specific submission.
     */
    public function show($id)
    {
        $submission = AssignmentSubmission::with(['user', 'versions', 'grade', 'comments', 'plagiarismReport'])
            ->findOrFail($id);

        // Check if user has access (owner, instructor, or admin)
        $user = Auth::user();
        if ($submission->user_id !== $user->id) {
            // Check if user is instructor for this course
            $courseInstructor = $submission->assignment->course->instructors()
                ->where('user_id', $user->id)
                ->exists();

            if (!$courseInstructor && !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized access to submission'], 403);
            }
        }

        return response()->json(['submission' => $submission]);
    }

    /**
     * Perform plagiarism check on a submission.
     */
    private function checkPlagiarism(AssignmentSubmission $submission)
    {
        // This would be implemented with an actual plagiarism detection service
        // For now, we'll just create a placeholder report

        // Get previous submissions for the same assignment
        $previousSubmissions = AssignmentSubmission::where('assignment_id', $submission->assignment_id)
            ->where('id', '!=', $submission->id)
            ->get();

        // Mock similarity calculation
        $similarityScore = rand(0, 30); // Random score between 0-30%

        // Create mock matched sources
        $matchedSources = [];
        if ($similarityScore > 5) {
            // Add some mock matched sources
            foreach ($previousSubmissions->take(3) as $prevSubmission) {
                $matchedSources[] = [
                    'source_type' => 'internal',
                    'submission_id' => $prevSubmission->id,
                    'user_id' => $prevSubmission->user_id,
                    'match_percentage' => rand(5, $similarityScore),
                ];
            }
        }

        // Create or update plagiarism report
        PlagiarismReport::updateOrCreate(
            ['assignment_submission_id' => $submission->id],
            [
                'similarity_score' => $similarityScore,
                'matched_sources' => $matchedSources,
                'similarity_details' => [
                    'text_matches' => rand(0, 5),
                    'source_count' => count($matchedSources),
                ],
                'checked_at' => Carbon::now(),
            ]
        );

        // Update submission with similarity score
        $submission->similarity_score = $similarityScore;
        $submission->save();
    }
}
