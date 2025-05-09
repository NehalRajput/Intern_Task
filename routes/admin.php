<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\InternController;

Route::middleware(['guest:admin'])->name('admin.')->group(function () {
    Route::prefix('admin')->controller(AuthController::class)->group(function () {
        Route::get('/login', 'showAdminLogin')->name('login');
        Route::post('/login', 'adminLogin')->name('authenticate');
    });
});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Intern Management
    Route::controller(InternController::class)->prefix('interns')->name('interns.')->group(function () {
        Route::get('/', 'index')->name('index')->can('read_interns');
        Route::get('/create', 'create')->name('create')->can('create_interns');
        Route::post('/', 'store')->name('store')->can('create_interns');
        Route::get('/{intern}/edit', 'edit')->name('edit')->can('update_interns');
        Route::put('/{intern}', 'update')->name('update')->can('update_interns');
        Route::delete('/{intern}', 'destroy')->name('destroy')->can('delete_interns');
    });

    /*
    // Chat System
    Route::controller(ChatController::class)->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/send', 'sendMessage')->name('send');
        Route::get('/messages/{user}', 'getMessages')->name('messages');
        Route::post('/mark-read', 'markAsRead')->name('mark-read');
    });*/
    
    Route::controller(CommentController::class)->group(function () {
        Route::get('/tasks/{task}/comments', 'index')->name('comments.index')->middleware('auth:admin');
        Route::post('/tasks/{task}/comments', 'store')->name('comments.store')->middleware('auth:admin');
        Route::put('/comments/{comment}', 'update')->name('comments.update')->middleware('auth:admin');
        Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy')->middleware('auth:admin');
    });

    // Admin Logout
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {
    // Admin Management
    Route::controller(AdminController::class)->prefix('admins')->name('admins.')->group(function () {
        Route::get('/', 'index')->name('index')->can('read_admins');
        Route::get('/create', 'create')->name('create')->can('create_admins');
        Route::post('/', 'store')->name('store')->can('create_admins');
        Route::get('/{admin}/edit', 'edit')->name('edit')->can('update_admins');
        Route::put('/{admin}', 'update')->name('update')->can('update_admins');
        Route::delete('/{admin}', 'destroy')->name('destroy')->can('delete_admins');
    });

    // Delete User
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('delete-user');

   

    Route::controller(TaskController::class)->group(function () {
        Route::get('/tasks', 'index')->name('tasks.index')->can('read_tasks');
        Route::get('/tasks/create', 'create')->name('tasks.create')->can('create_tasks');
        Route::post('/tasks', 'store')->name('tasks.store')->can('create_tasks');
        Route::get('/tasks/{task}/edit', 'edit')->name('tasks.edit')->can('update_tasks');
        Route::put('/tasks/{task}', 'update')->name('tasks.update')->can('update_tasks');
        Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy')->can('delete_tasks');
        Route::post('/tasks/{task}/assign-intern', 'assignIntern')->name('tasks.assign-intern');
        Route::delete('/tasks/{task}/interns/{intern}', 'detachIntern')->name('tasks.detach-intern');
    });
    
});
