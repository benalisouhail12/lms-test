<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('authentication::index');
});
Route::get('/dashboard', function () {
    return view('authentication::dashboard');
});
Route::get('/mycours', function () {
    return view('CourseManagement::mycours');
});
Route::prefix('api')->group(function () {
    require __DIR__ . '/../app/Modules/Authentication/Routes/api.php';
    Route::prefix('student')->group(function () {
        require __DIR__ . '/../app/Modules/StudentPortal/Routes/api.php';
    });
    Route::prefix('course')->group(function () {
        require __DIR__ . '/../app/Modules/CourseManagement/Routes/api.php';
    });
    Route::prefix('assingment-system')->group(function () {
        require __DIR__ . '/../app/Modules/AssignmentSystem/Routes/api.php';
    });
    
});
