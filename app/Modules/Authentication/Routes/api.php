<?php

use App\Modules\Authentication\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Module API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('/login/callback', [AuthController::class, 'loginCallback']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // User profile
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/sync-roles', [AuthController::class, 'syncRoles']);

       /*  // User profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

        // Roles and permissions (admin only)
        Route::middleware(['role:administrator'])->group(function () {
            Route::get('/roles', [RolePermissionController::class, 'listRoles']);
            Route::post('/roles', [RolePermissionController::class, 'createRole']);
            Route::get('/roles/{id}', [RolePermissionController::class, 'showRole']);
            Route::put('/roles/{id}', [RolePermissionController::class, 'updateRole']);
            Route::delete('/roles/{id}', [RolePermissionController::class, 'deleteRole']);

            Route::get('/permissions', [RolePermissionController::class, 'listPermissions']);
            Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
            Route::get('/permissions/{id}', [RolePermissionController::class, 'showPermission']);
            Route::put('/permissions/{id}', [RolePermissionController::class, 'updatePermission']);
            Route::delete('/permissions/{id}', [RolePermissionController::class, 'deletePermission']);

            Route::post('/roles/{roleId}/permissions', [RolePermissionController::class, 'assignPermissions']);
            Route::delete('/roles/{roleId}/permissions/{permissionId}', [RolePermissionController::class, 'removePermission']);

            Route::get('/users/{userId}/roles', [RolePermissionController::class, 'getUserRoles']);
            Route::post('/users/{userId}/roles', [RolePermissionController::class, 'assignRolesToUser']);
            Route::delete('/users/{userId}/roles/{roleId}', [RolePermissionController::class, 'removeRoleFromUser']);
        }); */
    });
});
