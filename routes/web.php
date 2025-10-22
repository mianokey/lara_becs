<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskMessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Attendance
Route::post('/attendance/toggle', [AttendanceController::class, 'toggle'])
    ->name('attendance.toggle')
    ->middleware('auth');

// Tasks (resource)
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');

    // Projects
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    // Users
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/tasks/{task}/messages', [TaskMessageController::class, 'index']);
    Route::post('/tasks/{task}/messages', [TaskMessageController::class, 'store']);
    Route::post('/tasks/{task}/messages/read', [TaskMessageController::class, 'markAsRead']);
    Route::get('/notifications/unread', [TaskMessageController::class, 'unreadCount']);

    Route::post('/notifications/{id}/read', [TaskMessageController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [TaskMessageController::class, 'markAllRead']);
});
