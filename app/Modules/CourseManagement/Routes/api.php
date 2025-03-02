<?php

use App\Modules\CourseManagement\Controllers\Api\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']); // List all courses
    Route::post('/', [CourseController::class, 'store']); // Create a new course
    Route::get('/{course}', [CourseController::class, 'show']); // Show a specific course
    Route::put('/{course}', [CourseController::class, 'update']); // Update a specific course
    Route::delete('/{course}', [CourseController::class, 'destroy']); // Delete a specific course

    Route::post('/{course}/sections', [CourseController::class, 'storeSections']); // Add a new section to a course
    Route::post('/{course}/sections/reorder', [CourseController::class, 'reorderSections']); // Reorder sections within a course

    Route::prefix('sections')->group(function () {
        Route::put('/{section}', [CourseController::class, 'updateSection']); // Update a section
        Route::delete('/{section}', [CourseController::class, 'destroySection']); // Delete a section
        Route::post('/{section}/lessons', [CourseController::class, 'storeLessons']); // Add a new lesson to a section
        Route::post('/{section}/lessons/reorder', [CourseController::class, 'reorderLessons']); // Reorder lessons within a section
    });

    Route::prefix('lessons')->group(function () {
        Route::put('/{lesson}', [CourseController::class, 'updateLesson']); // Update a lesson
        Route::delete('/{lesson}', [CourseController::class, 'destroyLesson']); // Delete a lesson
    });

    Route::post('/{course}/enroll', [CourseController::class, 'enrollStudent']); // Enroll a student in a course
    Route::post('/{course}/unenroll', [CourseController::class, 'unenrollStudent']); // Unenroll a student from a course
});

Route::prefix('progress')->group(function () {
    Route::get('/{course}', [CourseController::class, 'getStudentProgress']); // Get student progress in a course
    Route::post('/{lesson}', [CourseController::class, 'updateProgress']); // Update student progress in a lesson
});
