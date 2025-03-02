<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Notifications\Controllers\NotificationController;
use App\Modules\Notifications\Controllers\PreferenceController;
use App\Modules\Notifications\Controllers\HistoryController;


    // Notification endpoints
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::patch('/{id}/status', [NotificationController::class, 'updateStatus']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

        // History
        Route::get('/history', [HistoryController::class, 'index']);
    });

    // Preferences endpoints
    Route::prefix('preferences/notifications')->group(function () {
        Route::get('/', [PreferenceController::class, 'show']);
        Route::put('/', [PreferenceController::class, 'update']);
    });


