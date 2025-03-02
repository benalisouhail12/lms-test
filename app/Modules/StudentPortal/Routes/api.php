<?php

use App\Modules\StudentPortal\Controllers\AchievementController;
use App\Modules\StudentPortal\Controllers\AnalyticsController;
use App\Modules\StudentPortal\Controllers\EnrollmentController;
use App\Modules\StudentPortal\Controllers\ProfileController;
use App\Modules\StudentPortal\Controllers\ProgressController;
use Illuminate\Support\Facades\Route;

// Student Profile Management
Route::prefix('profile')->middleware(['auth:api'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::get('/academic-history', 'academicHistory');
        Route::post('/avatar', 'updateAvatar');
        Route::put('/preferences', 'updatePreferences');
    });
});

// Course Enrollment
Route::prefix('enrollment')->middleware(['auth:api'])->group(function () {
    Route::controller(EnrollmentController::class)->group(function () {
        Route::get('/available-courses', 'availableCourses');
        Route::get('/my-courses', 'enrolledCourses');
        Route::post('/enroll/{course}', 'enrollInCourse');
        Route::delete('/drop/{course}', 'dropCourse');
        Route::get('/learning-paths', 'availableLearningPaths');
        Route::post('/learning-path/{path}', 'enrollInLearningPath');
    });
});

// Progress Dashboard
Route::prefix('progress')->middleware(['auth:api'])->group(function () {
    Route::controller(ProgressController::class)->group(function () {
        Route::get('/overview', 'overview');
        Route::get('/course/{course}', 'courseProgress');
        Route::get('/learning-path/{path}', 'learningPathProgress');
        Route::post('/lesson/{lesson}/complete', 'markLessonComplete');
        Route::post('/lesson/{lesson}/track-time', 'trackTimeSpent');
    });
});

// Performance Analytics
Route::prefix('analytics')->middleware(['auth:api'])->group(function () {
    Route::controller(AnalyticsController::class)->group(function () {
        Route::get('/performance', 'performance');
        Route::get('/activities', 'activitiesPerformance');
        Route::get('/assessments', 'assessmentsResults');
        Route::get('/completion-rate', 'completionRate');
        Route::get('/engagement', 'engagementMetrics');
    });
});

// Achievement System
Route::prefix('achievements')->middleware(['auth:api'])->group(function () {
    Route::controller(AchievementController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/badges', 'badges');
        Route::get('/certificates', 'certificates');
        Route::get('/certificate/{course}/download', 'downloadCertificate');
    });
});
