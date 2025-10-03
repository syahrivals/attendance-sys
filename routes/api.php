<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Server is running',
        'timestamp' => now()
    ]);
});

// Attendance routes dengan controller
Route::prefix('attendance')->group(function () {
    Route::post('/tap', [AttendanceController::class, 'tap']);
    Route::get('/today', [AttendanceController::class, 'todayAttendances']);
    Route::post('/daily-reset', [AttendanceController::class, 'dailyReset']);
});

// Debug route
Route::get('/debug', function () {
    return response()->json([
        'laravel_version' => app()->version(),
        'php_version' => phpversion(),
        'routes_status' => 'API routes loaded properly',
        'timestamp' => now()
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});