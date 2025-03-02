<?php

use App\Modules\AssignmentSystem\Controllers\AssignmentController;
use App\Modules\AssignmentSystem\Controllers\AssignmentExtensionController;
use App\Modules\AssignmentSystem\Controllers\AssignmentGroupController;
use App\Modules\AssignmentSystem\Controllers\AssignmentSubmissionController;
use App\Modules\AssignmentSystem\Controllers\GradeController;
use App\Modules\AssignmentSystem\Controllers\GradingCriteriaController;
use App\Modules\AssignmentSystem\Controllers\SubmissionCommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assignment System API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Assignments
    Route::get('/courses/{courseId}/assignments', [AssignmentController::class, 'index']);
    Route::post('/assignments', [AssignmentController::class, 'store']);
    Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
    Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
    Route::post('/assignments/{id}/publish', [AssignmentController::class, 'publish']);
    Route::post('/assignments/{id}/archive', [AssignmentController::class, 'archive']);

    // Assignment Submissions
    Route::get('/assignments/{assignmentId}/submissions', [AssignmentSubmissionController::class, 'index']);
    Route::get('/assignments/{assignmentId}/users/{userId}/submissions', [AssignmentSubmissionController::class, 'studentSubmissions']);
    Route::post('/assignments/{assignmentId}/submit', [AssignmentSubmissionController::class, 'submit']);
    Route::get('/submissions/{id}', [AssignmentSubmissionController::class, 'show']);
    Route::put('/submissions/{id}', [AssignmentSubmissionController::class, 'update']);
    Route::post('/submissions/{id}/submit-draft', [AssignmentSubmissionController::class, 'submitDraft']);

    // Grades
    Route::post('/submissions/{submissionId}/grade', [GradeController::class, 'grade']);
    Route::get('/submissions/{submissionId}/grade', [GradeController::class, 'show']);

    // Grading Criteria
    Route::get('/assignments/{assignmentId}/criteria', [GradingCriteriaController::class, 'index']);
    Route::post('/assignments/{assignmentId}/criteria', [GradingCriteriaController::class, 'store']);
    Route::put('/criteria/{id}', [GradingCriteriaController::class, 'update']);
    Route::delete('/criteria/{id}', [GradingCriteriaController::class, 'destroy']);

    // Submission Comments
    Route::get('/submissions/{submissionId}/comments', [SubmissionCommentController::class, 'index']);
    Route::post('/submissions/{submissionId}/comments', [SubmissionCommentController::class, 'store']);
    Route::put('/comments/{id}', [SubmissionCommentController::class, 'update']);
    Route::delete('/comments/{id}', [SubmissionCommentController::class, 'destroy']);

    // Assignment Groups
    Route::get('/assignments/{assignmentId}/groups', [AssignmentGroupController::class, 'index']);
    Route::post('/assignments/{assignmentId}/groups', [AssignmentGroupController::class, 'store']);
    Route::put('/groups/{id}', [AssignmentGroupController::class, 'update']);
    Route::delete('/groups/{id}', [AssignmentGroupController::class, 'destroy']);

    // Assignment Extensions
    Route::post('/assignments/{assignmentId}/extensions', [AssignmentExtensionController::class, 'grantExtension']);
    Route::delete('/assignments/{assignmentId}/extensions/{userId}', [AssignmentExtensionController::class, 'revokeExtension']);
    Route::get('/assignments/{assignmentId}/extensions', [AssignmentExtensionController::class, 'listExtensions']);
});
