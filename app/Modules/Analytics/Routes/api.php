<?php

use app\Modules\Analytics\Controllers\DashboardController;
use app\Modules\Analytics\Controllers\ExportController;
use app\Modules\Analytics\Controllers\MetricsController;
use app\Modules\Analytics\Controllers\ReportController;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->prefix('api')->group(function () {

    Route::get('/dashboard/metrics', [DashboardController::class, 'getMetrics']);
    Route::get('/dashboard/indicators', [DashboardController::class, 'getPerformanceIndicators']);
    Route::get('/dashboard/courses', [DashboardController::class, 'getCourseAnalysis']);


    Route::apiResource('reports', ReportController::class);

    Route::apiResource('metrics', MetricsController::class)->except(['update', 'destroy']);

    Route::post('/export', [ExportController::class, 'export']);
});
