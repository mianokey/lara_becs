<?php

use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskMessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() {
    return 'Laravel works!';
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
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');



    // Task Messages & Notifications
    Route::get('/tasks/{task}/messages', [TaskMessageController::class, 'index']);
    Route::post('/tasks/{task}/messages', [TaskMessageController::class, 'store']);
    Route::post('/tasks/{task}/messages/read', [TaskMessageController::class, 'markAsRead']);
    Route::get('/notifications/unread', [TaskMessageController::class, 'unreadCount']);

    Route::post('/notifications/{id}/read', [TaskMessageController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [TaskMessageController::class, 'markAllRead']);

    Route::get('pettycash', [PettyCashController::class, 'index'])->name('pettycash.index');
    Route::get('pettycash/create', [PettyCashController::class, 'create'])->name('pettycash.create');
    Route::post('pettycash', [PettyCashController::class, 'store'])->name('pettycash.store');
    Route::patch('pettycash/{pettyCash}/approve', [PettyCashController::class, 'approve'])->name('pettycash.approve');
    Route::patch('pettycash/{pettyCash}/reject', [PettyCashController::class, 'reject'])->name('pettycash.reject');

    Route::get('roles', [RolePermissionController::class, 'rolesIndex'])->name('admin.roles.index');
    Route::get('roles/create', [RolePermissionController::class, 'createRole'])->name('admin.roles.create');
    Route::post('roles', [RolePermissionController::class, 'storeRole'])->name('admin.roles.store');
    Route::get('roles/{id}/edit', [RolePermissionController::class, 'editRole'])->name('admin.roles.edit');
    Route::put('roles/{id}', [RolePermissionController::class, 'updateRole'])->name('admin.roles.update');
    Route::delete('roles/{id}', [RolePermissionController::class, 'deleteRole'])->name('admin.roles.delete');
    // ==========================
    // PERMISSIONS
    // ==========================
    Route::get('permissions', [RolePermissionController::class, 'permissionsIndex'])->name('admin.permissions.index');
    Route::get('permissions/create', [RolePermissionController::class, 'createPermission'])->name('admin.permissions.create');
    Route::post('permissions', [RolePermissionController::class, 'storePermission'])->name('admin.permissions.store');
    Route::get('permissions/{id}/edit', [RolePermissionController::class, 'editPermission'])->name('admin.permissions.edit');
    Route::put('permissions/{id}', [RolePermissionController::class, 'updatePermission'])->name('admin.permissions.update');
    Route::delete('permissions/{id}', [RolePermissionController::class, 'deletePermission'])->name('admin.permissions.delete');
});
