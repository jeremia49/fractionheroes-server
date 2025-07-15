<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GameController;

// Authentication Routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Student Routes
    Route::resource('students', StudentController::class);
    

    
    // Game Routes
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
    Route::get('/students/{studentId}/games', [GameController::class, 'studentSessions'])->name('games.student-sessions');
    Route::get('/games/faces', [GameController::class, 'faces'])->name('games.faces');
    Route::get('/games/faces/{id}', [GameController::class, 'showFace'])->name('games.show-face');
    Route::patch('/games/{id}/notes', [GameController::class, 'updateNotes'])->name('games.update-notes');
    Route::patch('/games/faces/{id}/notes', [GameController::class, 'updateFaceNotes'])->name('games.update-face-notes');
});
