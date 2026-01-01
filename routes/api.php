<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskAssignmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All API routes for Task Management System
|--------------------------------------------------------------------------
*/

// --------------------
// Public Routes
// --------------------
Route::post('/login', [AuthController::class, 'login']);

// --------------------
// Protected Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });

    // Users API
    Route::apiResource('users', UserController::class);

    // Tasks API
    Route::apiResource('tasks', TaskController::class);

    // Task Assignments
    Route::post('/tasks/{id}/assign', [TaskAssignmentController::class, 'assign']);
    Route::get('/tasks/{id}/assignees', [TaskAssignmentController::class, 'assignees']);
    Route::delete('/tasks/{id}/assignees/{user_id}', [
        TaskAssignmentController::class,
        'remove'
    ]);
});
