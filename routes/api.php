<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    // Authentication Routes
    // Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'studentLogin']);
    
    Route::apiResource('students', StudentController::class);
    
    Route::get('/user', [AuthController::class, 'user']);

    
    // Game Routes
    Route::post('/games/start-session', [GameController::class, 'startSession']);
    Route::post('/games/capture-face', [GameController::class, 'captureFace']);
    Route::post('/games/update-progress', [GameController::class, 'updateProgress']);
    Route::post('/games/end-session', [GameController::class, 'endSession']);
    Route::get('/games/session/{sessionId}', [GameController::class, 'getSession']);
    Route::get('/students/{studentId}/game-history', [GameController::class, 'getStudentHistory']);
    Route::get('/students/{studentId}/level-status', [GameController::class, 'getLevelStatus']);
    Route::get('/leaderboard', [GameController::class, 'leaderboard']);
    
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
}); 