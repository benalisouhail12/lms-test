<?php

namespace App\Modules\AssignmentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AssignmentSystem\Models\Assignment;
use App\Modules\AssignmentSystem\Models\AssignmentSubmission;
use App\Modules\AssignmentSystem\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class GradeController extends Controller
{
    /**
     * Grade a submission.
     */
    public function grade(Request $request, $submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        // Ensure user has permission to grade (instructor or admin)
        $user = Auth::user();
        $courseInstructor = $submission->assignment->course->instructors()
            ->where('user_id', $user->id)
            ->exists();

        if (!$courseInstructor && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized to grade submission'], 403);
        }

        $validator = Validator::make($request->all(), [
            'points_earned' => 'required|numeric|min:0',
            'is_final' => 'boolean',
            'criteria_grades' => 'nullable|array',
            'criteria_grades.*.criteria_id' => 'required|exists:grading_criteria,id',
            'criteria_grades.*.points_earned' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create or update grade
        $grade = Grade::updateOrCreate(
            ['assignment_submission_id' => $submissionId],
            [
                'points_earned' => $request->points_earned,
                'points_possible' => $submission->assignment->max_points,
                'percentage' => ($request->points_earned / $submission->assignment->max_points) * 100,
                'letter_grade' => $this->calculateLetterGrade($request->points_earned, $submission->assignment->max_points),
                'graded_by' => $user->id,
                'graded_at' => Carbon::now(),
                'is_final' => $request->is_final ?? false,
            ]
        );

        // Handle criteria grades if provided
        if ($request->has('criteria_grades')) {
            foreach ($request->criteria_grades as $criteriaGrade) {
                $grade->criteriaGrades()->updateOrCreate(
                    ['grading_criteria_id' => $criteriaGrade['criteria_id']],
                    [
                        'points_earned' => $criteriaGrade['points_earned'],
                        'comment' => $criteriaGrade['comment'] ?? null,
                    ]
                );
            }
        }

        // Update submission status if grade is final
        if ($request->is_final ?? false) {
            $submission->status = 'GRADED';
            $submission->save();
        }

        return response()->json([
            'grade' => $grade->load('criteriaGrades'),
            'submission' => $submission,
        ]);
    }

    /**
     * Calculate letter grade based on percentage.
     */
    private function calculateLetterGrade($earned, $possible)
    {
        $percentage = ($earned / $possible) * 100;

        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Get grade details for a submission.
     */
    public function show($submissionId)
    {
        $grade = Grade::where('assignment_submission_id', $submissionId)
            ->with(['criteriaGrades', 'gradedBy'])
            ->firstOrFail();

        $submission = AssignmentSubmission::findOrFail($submissionId);

        // Check if user has access to view this grade
        $user = Auth::user();
        if ($submission->user_id !== $user->id) {
            // Check if user is instructor for this course
            $courseInstructor = $submission->assignment->course->instructors()
                ->where('user_id', $user->id)
                ->exists();

            if (!$courseInstructor && !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized to view grade'], 403);
            }
        }

        return response()->json(['grade' => $grade]);
    }
}
